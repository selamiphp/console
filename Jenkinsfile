pipeline {
  agent {
    docker {
      image 'phpdockerio/php72-cli'
    }

  }
  stages {
    stage('Clone repository') {
      steps {
        git(url: 'https://github.com/selamiphp/console.git', branch: 'master')
      }
    }
    stage('install') {
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