pipeline {
  agent any
  stages {
    stage('Get files') {
      parallel {
        stage('Get files') {
          steps {
            git(url: 'https://github.com/selamiphp/console.git', branch: 'master')
          }
        }
        stage('get composer') {
          steps {
            sh 'curl -sS https://getcomposer.org/installer | php'
            sh 'mv composer.phar /usr/local/bin/composer'
          }
        }
        
        
        stage('run composer') {
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
