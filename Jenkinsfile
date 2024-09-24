pipeline {
    agent any

    options {
        disableConcurrentBuilds()
        skipDefaultCheckout(true)
    }
    stages {
        stage('Checkout') {
            steps {
                cleanWs()
                checkout scm
            }
        }
        stage('Code Quality Analysis') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'SONARQUBE', variable: 'SONARQUBE')]) {
                        sh """
                            docker run \
                            --rm \
                            -e SONAR_HOST_URL="https://sonarcloud.io/" \
                            -e SONAR_SCANNER_OPTS="-Dsonar.projectKey=${JOB_NAME} -Dsonar.organization=\"caiohenrks\" -Dsonar.languages=js,Docker,PHP -Dsonar.projectVersion=V${BUILD_NUMBER}" \
                            -e SONAR_TOKEN="${SONARQUBE}" \
                            -v "./data_app:/usr/src" \
                            sonarsource/sonar-scanner-cli
                        """
        				sleep 15
        				
                        def qualityGate = sh(script: "curl -k -u ${SONARQUBE}: https://sonarcloud.io/api/qualitygates/project_status?projectKey=${JOB_NAME}", returnStdout: true).trim()
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
        stage('Build Image') {
            steps {
                sh "docker-compose -f ./data_app/docker-compose.yml build"
                sh "docker build -t ${JOB_NAME.toLowerCase()} -f ./data_app/Dockerfile ./data_app"
            }
        }
        stage('Security Image Scan') {
            steps {
                sh 'curl -L -o html.tpl https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/html.tpl'
                sh """
                trivy image --exit-code 0 --severity HIGH,CRITICAL \
                --format template --template @html.tpl \
                --output trivy-report.html ${JOB_NAME.toLowerCase()}
                """

                /*
                sh """
                trivy image --exit-code 0 --severity HIGH,CRITICAL \
                --format json \
                --output trivy-report.json ${JOB_NAME.toLowerCase()}
                """
                */
            }
        }
        stage('Push to Registry') {
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
        stage('Deploy Application') {
            steps {
                sh "docker-compose -f ./data_app/docker-compose.yml up -d"
            }
        }
        stage('Smoke Test') {
            steps {
                sh "docker run --rm -v ./postman:/etc/newman --user 0 -t newman-reporter run /etc/newman/${JOB_NAME.toLowerCase()}.json -r htmlextra"
                sh """
                    mkdir -p artifacts
                    sudo mv ./postman/newman/* artifacts/
                    sudo mv ./trivy-report.html artifacts/
                """
            }
        }
        stage('Upload Artifacts') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'NEXUS_LOGIN', usernameVariable: 'NEXUS_USERNAME', passwordVariable: 'NEXUS_PASSWORD')]) {
                  sh "zip -r ${JOB_NAME.toLowerCase()}_${BUILD_NUMBER}.zip artifacts"
                  archiveArtifacts artifacts: "${JOB_NAME.toLowerCase()}_${BUILD_NUMBER}.zip", allowEmptyArchive: true
                  sh "curl -v -u ${NEXUS_USERNAME}:${NEXUS_PASSWORD} --upload-file ${JOB_NAME.toLowerCase()}_${BUILD_NUMBER}.zip http://192.168.50.30:8081/repository/jenkins_artifacts/${JOB_NAME.toLowerCase()}_${BUILD_NUMBER}.zip"
                }
            }
        }
    }
}
