pipeline {
   agent any
   stages {
       stage('Build and Deploy Code') {
           steps {
               script {
                   echo 'Building and deploying code'
               }
           }
       }
       stage('Test') {
           steps {
               script {
                   echo 'Running tests'
               }
           }
       }
   }
}


pipeline {
    agent any
    stages {
        stage('Build and Deploy Code') {
            steps {
                script {
                    try {
                        // Checkout the code from version control
                        git 'https://github.com/my-username/my-repo.git'

                        // Build and deploy the code
                        sh "echo 'Building and deploying code'"
                    } catch (err) {
                        // If there is an error, send a notification
                        echo "Error building and deploying code: ${err}"
                        currentBuild.result = 'FAILURE'
                    }
                }
            }
        }
        stage('Test') {
            steps {
                script {
                    try {
                        // Run tests
                        sh "echo 'Running tests'"
                        // If tests fail, send a notification
                        sh "test-command"
                        if (currentBuild.result == 'FAILURE') {
                            echo "Tests failed"
                            currentBuild.result = 'FAILURE'
                        }
                    } catch (err) {
                        // If there is an error, send a notification
                        echo "Error running tests: ${
