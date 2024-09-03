pipeline {
    agent any

<<<<<<< HEAD
    stages { 
=======
    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        } 
>>>>>>> 4dbd39e4d45fe664a25af6d771aae959541911d0
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
                            -v "./${JOB_NAME}/data_app:/usr/src" \
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
<<<<<<< HEAD
        stage('Build') {
            steps {
                sh "docker-compose -f ./data_app/docker-compose.yml build"
                sh "docker build -t ${JOB_NAME.toLowerCase()} -f ./data_app/Dockerfile ./data_app"
            }
        }
        stage('Push Nexus') {
            steps {
                script {
                    def nexusUrl = '192.168.50.30:8082'
                    def repoName = 'docker'
        
                    withCredentials([usernamePassword(credentialsId: 'NEXUS_LOGIN', usernameVariable: 'NEXUS_USERNAME', passwordVariable: 'NEXUS_PASSWORD')]) {
                        sh "docker login -u $NEXUS_USERNAME -p $NEXUS_PASSWORD ${nexusUrl}"
                        sh "docker tag ${JOB_NAME.toLowerCase()} ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:latest"
                        sh "docker push ${nexusUrl}/${repoName}/${JOB_NAME.toLowerCase()}:latest"
                    }
                }
            }
        } 
=======
>>>>>>> 4dbd39e4d45fe664a25af6d771aae959541911d0
    }
}