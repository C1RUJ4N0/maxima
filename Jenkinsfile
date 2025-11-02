pipeline {
    agent any 

    environment {
        // --- Variables de Configuración AWS (Tus Datos) ---
        AWS_ACCOUNT_ID = '830099649054' 
        AWS_REGION     = 'us-east-1' 
        ECR_REPO_NAME  = 'maxima-app' 
        ECS_CLUSTER    = 'maxima-cluster'     
        ECS_SERVICE    = 'maxima-app-task-def-service-srg8a7n3' // Nuevo nombre de servicio
        TASK_DEF_FAMILY = "maxima-app-task-def"
        
        // IDs de las Credenciales de Secret Text
        AWS_ACCESS_ID_CRED = 'aws-access-key' 
        AWS_SECRET_KEY_CRED = 'aws-secret-key' 

        // Variables generadas
        IMAGE_TAG      = "${env.BUILD_NUMBER}" 
        ECR_REGISTRY   = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
        ECR_IMAGE_URI  = "${ECR_REGISTRY}/${ECR_REPO_NAME}:${IMAGE_TAG}"
    }

    stages {
        stage('1. Checkout Code') {
            steps {
                echo "Código obtenido de GitHub."
            }
        }
        
        stage('2. Build Docker Image') {
            steps {
                echo "Construyendo imagen: ${ECR_REPO_NAME}:${IMAGE_TAG}"
                sh "docker build -t ${ECR_REPO_NAME}:${IMAGE_TAG} ."
            }
        }

        stage('3. Push to ECR') {
            steps {
                // Inyecta las claves de Secret Text como variables de entorno
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
                        sh "docker tag ${ECR_REPO_NAME}:${IMAGE_TAG} ${ECR_IMAGE_URI}"
                        echo "Subiendo imagen a ECR: ${ECR_IMAGE_URI}"
                        sh "docker push ${ECR_IMAGE_URI}"
                    }
                }
            }
        }

        stage('4. Deploy to ECS') {
            steps {
                // Reaplicamos la inyección para el despliegue de ECS
                withCredentials([
                    [ $class: 'StringBinding', credentialsId: AWS_ACCESS_ID_CRED, variable: 'AWS_ACCESS_KEY_ID' ],
                    [ $class: 'StringBinding', credentialsId: AWS_SECRET_KEY_CRED, variable: 'AWS_SECRET_ACCESS_KEY' ]
                ]) {
                    script {
                        // A. Reemplazar la imagen en el JSON con el nuevo tag
                        echo "Actualizando task-definition.json con el nuevo tag: ${IMAGE_TAG}"
                        sh "sed -i 's|${ECR_REPO_NAME}:latest|${ECR_REPO_NAME}:${IMAGE_TAG}|g' task-definition.json"

                        // B. LIMPIEZA CRÍTICA: Eliminar caracteres de retorno de carro de Windows (\r)
                        echo "Limpiando formato de archivo JSON..."
                        sh "tr -d '\\r' < task-definition.json > task-definition-clean.json" 

                        // C. Registrar la nueva revisión de la definición de tarea (usando el archivo limpio)
                        echo "Registrando nueva revisión de tarea..."
                        sh "aws ecs register-task-definition --cli-input-json file://task-definition-clean.json --region ${AWS_REGION}"
                        
                        // D. Actualizar el servicio ECS (USANDO LA ÚLTIMA REVISIÓN ACTIVA)
                        echo "Forzando nuevo despliegue en servicio ${ECS_SERVICE}..."
                        sh "aws ecs update-service --cluster ${ECS_CLUSTER} --service ${ECS_SERVICE} --task-definition ${TASK_DEF_FAMILY} --region ${AWS_REGION}"
                        
                        // E. Esperar a que el despliegue finalice (Opcional)
                        echo "Esperando a que el servicio ${ECS_SERVICE} esté estable..."
                        sh "aws ecs wait services-stable --cluster ${ECS_CLUSTER} --services ${ECS_SERVICE} --region ${AWS_REGION}"
                    }
                }
            }
        }
    }
    
    post {
        always {
            // Limpieza: Elimina la imagen local
            sh "docker rmi ${ECR_REPO_NAME}:${IMAGE_TAG}"
        }
    }
}
