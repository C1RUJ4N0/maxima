pipeline {
    agent any 

    environment {
        AWS_ACCOUNT_ID   = "123456789012"
        AWS_REGION       = "us-east-1"
        ECR_REPOSITORY   = "maxima-app"
        ECS_CLUSTER_NAME = "maxima-cluster"
        ECS_SERVICE_NAME = "maxima-service"
        AWS_CREDS_ID     = "830099649054" 
    }

    stages {

        stage('Checkout') {
            steps {
                echo "1. Descargando código de GitHub..."
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo "2. Construyendo la imagen de Docker (para producción)..."
                    def imageTag = env.GIT_COMMIT.take(7)

                    env.IMAGE_NAME_WITH_TAG = "${env.AWS_ACCOUNT_ID}.dkr.ecr.${env.AWS_REGION}.amazonaws.com/${env.ECR_REPOSITORY}:${imageTag}"
                    env.IMAGE_NAME_LATEST = "${env.AWS_ACCOUNT_ID}.dkr.ecr.${env.AWS_REGION}.amazonaws.com/${env.ECR_REPOSITORY}:latest"

                    sh "docker build -t ${env.IMAGE_NAME_WITH_TAG} ."

                    sh "docker tag ${env.IMAGE_NAME_WITH_TAG} ${env.IMAGE_NAME_LATEST}"
                }
            }
        }

        stage('Push Image to AWS ECR') {
            steps {
                echo "3. Subiendo imagen a AWS ECR..."
                ecrLogin(credentialsId: env.AWS_CREDS_ID, region: env.AWS_REGION)

                sh "docker push ${env.IMAGE_NAME_WITH_TAG}"
                sh "docker push ${env.IMAGE_NAME_LATEST}"
            }
        }

        stage('Deploy to AWS ECS') {
            steps {
                echo "4. Actualizando el servicio en AWS ECS..."
                withAWS(credentials: env.AWS_CREDS_ID, region: env.AWS_REGION) {

                    sh "aws ecs update-service --cluster ${env.ECS_CLUSTER_NAME} --service ${env.ECS_SERVICE_NAME} --force-new-deployment"
                }
            }
        }
    }

    post {
        always {
            echo "5. Limpiando el workspace."
            cleanWs()
        }
    }
}
