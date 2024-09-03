pipeline {
    agent any

    stages { 
        stage('Scan Sonarqube') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'SONARQUBE', variable: 'SONARQUBE')]) {
                        sh """
                            docker run \
                            --rm \
                            -e SONAR_HOST_URL="http://192.168.50.20:9000/" \
                            -e SONAR_SCANNER_OPTS="-Dsonar.projectKey=${JOB_NAME}" \
                            -e SONAR_TOKEN="${SONARQUBE}" \
                            -v "./data_app:/usr/src" \
                            sonarsource/sonar-scanner-cli
                        """
        				sleep 15
        				
                        def qualityGate = sh(script: "curl -k -u ${SONARQUBE}: http://192.168.50.20:9000/api/qualitygates/project_status?projectKey=${JOB_NAME}", returnStdout: true).trim()
        				echo "Quality Gate JSON: ${qualityGate}"
        				
        				def status = new groovy.json.JsonSlurper().parseText(qualityGate).projectStatus.status
        				echo "SonarQube Quality Gate Status: ${status}"
        				
        				if (status != 'OK') {
        					error "Qualidade do código não atingiu o nível esperado: ${status}"
        				}
        			}
        		}
        	}
        }
        stage('Build') {
            steps {
                sh "docker-compose -f ./data_app/docker-compose.yml build"
                sh "docker build -t ${JOB_NAME.toLowerCase()} -f ./data_app/Dockerfile ./data_app"
            }
        }
        stage('Push Registry') {
            steps {
                script {
                    def nexusUrl = '192.168.50.30:8082'
                    def repoName = 'docker'
        
                    withCredentials([usernamePassword(credentialsId: 'NEXUS_LOGIN', usernameVariable: 'NEXUS_USERNAME', passwordVariable: 'NEXUS_PASSWORD')]) {
                        sh "docker login -u $NEXUS_USERNAME -p $NEXUS_PASSWORD ${nexusUrl}"
                        
                        sh "docker tag ${JOB_NAME.toLowerCase()} ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:latest"
                        sh "docker push ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:latest"
                        
                        sh "docker tag ${JOB_NAME.toLowerCase()} ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:V${BUILD_NUMBER}"
                        sh "docker push ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:V${BUILD_NUMBER}"
                    }
                }
            }
        }
        stage('Deploy') {
            steps {
                sh "docker-compose -f ./data_app/docker-compose.yml up -d"
            }
        }
        stage('Tests') {
            steps {
                sh "docker run -v ./postman:/etc/newman -t postman/newman run /etc/newman/${JOB_NAME.toLowerCase()}.json --reporters cli,html --reporter-html-export /etc/newman/report.html | tee output.log"
                def exitCode = sh(script: "grep -q 'failed' output.log; echo \$?", returnStatus: true)
                if (exitCode != 0) {
                    error "Os testes falharam. Veja o relatório HTML para mais detalhes."
                }
            }
        }
    }
}
