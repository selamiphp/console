pipeline {
  agent {
    docker {
      image 'phpdockerio/php72-cli'
    }

  }
  stages {
    stage('Get files') {
      parallel {
        stage('Clone repository') {
          steps {
            git(url: 'https://github.com/selamiphp/console.git', branch: 'master')
          }
        }
        stage('run composer') {
          steps {
            sh 'composer install'
          }
        }
        stage('test') {
          steps {
            sh 'ls -la'
            sh 'composer run unit-tests'
          }
        }
      }
    }
  }
}