pipeline {
    agent any 

    environment {
        // --- Variables de Configuración AWS ---
        AWS_ACCOUNT_ID = '830099649054' 
        AWS_REGION     = 'us-east-1' 
        ECS_CLUSTER    = 'maxima-cluster'     
        ECS_SERVICE    = 'maxima-app-task-def-service-srg8a7n3' 
        TASK_DEF_FAMILY = "maxima-app-task-def"
        
        // Repositorios ECR (App y Nginx)
        ECR_APP_REPO_NAME  = 'maxima-app' 
        ECR_NGINX_REPO_NAME = 'maxima-nginx' // ¡NUEVO! (Asegúrate de crear este repo en ECR)

        // IDs de Credenciales
        AWS_ACCESS_ID_CRED = 'aws-access-key' 
        AWS_SECRET_KEY_CRED = 'aws-secret-key' 

        // Variables generadas
        IMAGE_TAG      = "${env.BUILD_NUMBER}" 
        ECR_REGISTRY   = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
        ECR_APP_IMAGE_URI  = "${ECR_REGISTRY}/${ECR_APP_REPO_NAME}:${IMAGE_TAG}"
        ECR_NGINX_IMAGE_URI = "${ECR_REGISTRY}/${ECR_NGINX_REPO_NAME}:${IMAGE_TAG}"
    }

    stages {
        stage('1. Build Docker Images') {
            steps {
                echo "Construyendo imagen App: ${ECR_APP_REPO_NAME}:${IMAGE_TAG}"
                sh "docker build -t ${ECR_APP_REPO_NAME}:${IMAGE_TAG} -f Dockerfile ."
                
                echo "Construyendo imagen Nginx: ${ECR_NGINX_REPO_NAME}:${IMAGE_TAG}"
                sh "docker build -t ${ECR_NGINX_REPO_NAME}:${IMAGE_TAG} -f nginx.dockerfile ."
            }
        }

        stage('2. Push to ECR') {
            steps {
                withCredentials([
                    [ $class: 'StringBinding', credentialsId: AWS_ACCESS_ID_CRED, variable: 'AWS_ACCESS_KEY_ID' ],
                    [ $class: 'StringBinding', credentialsId: AWS_SECRET_KEY_CRED, variable: 'AWS_SECRET_ACCESS_KEY' ]
                ]) {
                    script {
                        echo "Obteniendo token de autenticación de ECR..."
                        def ecrCredentials = sh(
                            script: "aws ecr get-login-password --region ${AWS_REGION}", 
                            returnStdout: true
                        ).trim()

                        sh "echo ${ecrCredentials} | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
                        
                        // Push de la App
                        sh "docker tag ${ECR_APP_REPO_NAME}:${IMAGE_TAG} ${ECR_APP_IMAGE_URI}"
                        echo "Subiendo imagen App a ECR: ${ECR_APP_IMAGE_URI}"
                        sh "docker push ${ECR_APP_IMAGE_URI}"

                        // Push de Nginx
                        sh "docker tag ${ECR_NGINX_REPO_NAME}:${IMAGE_TAG} ${ECR_NGINX_IMAGE_URI}"
                        echo "Subiendo imagen Nginx a ECR: ${ECR_NGINX_IMAGE_URI}"
                        sh "docker push ${ECR_NGINX_IMAGE_URI}"
                    }
                }
            }
        }

        stage('3. Deploy to ECS') {
            steps {
                withCredentials([
                    [ $class: 'StringBinding', credentialsId: AWS_ACCESS_ID_CRED, variable: 'AWS_ACCESS_KEY_ID' ],
                    [ $class: 'StringBinding', credentialsId: AWS_SECRET_KEY_CRED, variable: 'AWS_SECRET_ACCESS_KEY' ]
                ]) {
                    script {
                        echo "Actualizando y limpiando task-definition.json..."
                        
                        // Actualizar y Limpiar (BOM, CR, y AMBAS imágenes)
                        sh "sed -i -e '1s/^\\xEF\\xBB\\xBF//' -e 's|${ECR_APP_REPO_NAME}:latest|${ECR_APP_REPO_NAME}:${IMAGE_TAG}|g' -e 's|${ECR_NGINX_REPO_NAME}:latest|${ECR_NGINX_REPO_NAME}:${IMAGE_TAG}|g' -e 's/\r\$//g' task-definition.json"

                        echo "Registrando nueva revisión de tarea..."
                        sh "aws ecs register-task-definition --cli-input-json file://task-definition.json --region ${AWS_REGION}"
                        
                        echo "Forzando nuevo despliegue en servicio ${ECS_SERVICE}..."
                        sh "aws ecs update-service --cluster ${ECS_CLUSTER} --service ${ECS_SERVICE} --task-definition ${TASK_DEF_FAMILY} --region ${AWS_REGION}"
                        
                        echo "Esperando a que el servicio ${ECS_SERVICE} esté estable..."
                        sh "aws ecs wait services-stable --cluster ${ECS_CLUSTER} --services ${ECS_SERVICE} --region ${AWS_REGION}"
                    }
                }
            }
        }
    }
    
    post {
        always {
            // Limpieza: Elimina ambas imágenes locales
            sh "docker rmi ${ECR_APP_REPO_NAME}:${IMAGE_TAG}"
            sh "docker rmi ${ECR_NGINX_REPO_NAME}:${IMAGE_TAG}"
        }
    }
}
