pipeline {
  agent {
    node {
      label 'php'
    }

  }
  stages {
    stage('Get files') {
      parallel {
        stage('Get files') {
          steps {
            git(url: 'https://github.com/selamiphp/console.git', branch: 'master')
          }
        }
        stage('run redis') {
          steps {
            sh 'composer install'
          }
        }
        stage('test') {
          steps {
            sh 'composer run unit-tests'
          }
        }
      }
    }
  }
}