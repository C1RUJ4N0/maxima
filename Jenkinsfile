pipeline {
    agent any 

    environment {
        // --- Variables de Configuración AWS (Tus Datos) ---
        AWS_ACCOUNT_ID = '830099649054' 
        AWS_REGION     = 'us-east-1' 
        ECR_REPO_NAME  = 'maxima-app' 
        ECS_CLUSTER    = 'maxima-cluster'     
        ECS_SERVICE    = 'maxima-app-task-def-service-etjvk2u9' 
        TASK_DEF_FAMILY = "maxima-app-task-def"
        
        // ID de la Credencial de AWS. Usamos el UUID: 3648b605-1bc3-4b5d-ac56-9b667b91381c
        AWS_CREDENTIALS_ID = '3648b605-1bc3-4b5d-ac56-9b667b91381c' 
        
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
                // SOLUCIÓN DEFINITIVA: Usa la directiva 'aws' para el tipo de credencial 'AWS Credentials'
                withCredentials([
                    aws(credentialsId: AWS_CREDENTIALS_ID)
                ]) {
                    script {
                        echo "Obteniendo token de autenticación de ECR..."
                        
                        // 1. Obtener token de autenticación de ECR
                        def ecrCredentials = sh(
                            script: "aws ecr get-login-password --region ${AWS_REGION}", 
                            returnStdout: true
                        ).trim()

                        // 2. Login de Docker
                        sh "echo ${ecrCredentials} | docker login --username AWS --password-stdin ${ECR_REGISTRY}"

                        // 3. Etiquetar y Subir
                        sh "docker tag ${ECR_REPO_NAME}:${IMAGE_TAG} ${ECR_IMAGE_URI}"
                        echo "Subiendo imagen a ECR: ${ECR_IMAGE_URI}"
                        sh "docker push ${ECR_IMAGE_URI}"
                    }
                }
            }
        }

        stage('4. Deploy to ECS') {
            steps {
                // Reaplicamos la directiva 'aws' para la etapa de despliegue
                withCredentials([
                    aws(credentialsId: AWS_CREDENTIALS_ID)
                ]) {
                    script {
                        // A. Reemplazar la imagen en el JSON con el nuevo tag
                        echo "Actualizando task-definition.json con el nuevo tag: ${IMAGE_TAG}"
                        // Nota: el sed -i '' es crucial en entornos basados en macOS (que usa /bin/sh)
                        sh "sed -i '' 's|${ECR_REPO_NAME}:latest|${ECR_REPO_NAME}:${IMAGE_TAG}|g' task-definition.json"

                        // B. Registrar la nueva revisión de la definición de tarea
                        echo "Registrando nueva revisión de tarea..."
                        sh "aws ecs register-task-definition --cli-input-json file://task-definition.json --region ${AWS_REGION}"
                        
                        // C. Actualizar el servicio ECS para usar la NUEVA revisión
                        echo "Forzando nuevo despliegue en servicio ${ECS_SERVICE}..."
                        sh "aws ecs update-service --cluster ${ECS_CLUSTER} --service ${ECS_SERVICE} --task-definition ${TASK_DEF_FAMILY}:${IMAGE_TAG} --region ${AWS_REGION}"
                        
                        // D. Esperar a que el despliegue finalice (Opcional)
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
