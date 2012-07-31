include settings.mk
include jenkins.mk

.PHONY:install
install: jenkins-install

.PHONY:install-latest
install-latest: jenkins-install-latest

PHONY:clean
clean: jenkins-clean
