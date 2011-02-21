include settings.mk
include jenkins/jenkins.mk

HORDE_FRAMEWORK=workdir/jobs/horde-git/workspace/framework
TOOLSDIR=workdir/jobs/php-ci-tools/workspace/pear/pear

JOBS=Alarm \
     Auth \
     Autoloader \
     Argv \
     Cli_Modular \
     Constraint \
     Controller \
     Core \
     Crypt \
     Date \
     Date_Parser \
     Db \
     Exception \
     Http \
     Injector \
     Itip \
     Kolab_Config \
     Kolab_Format \
     Kolab_Session \
     Ldap \
     Log \
     LoginTasks \
     Mail \
     Mime \
     Pdf \
     Prefs \
     Secret \
     Share \
     Stream_Filter \
     Support \
     Text_Flowed \
     Token \
     Translation \
     Url \
     Util \
     Xml_Element \
     Yaml

.PHONY:install
install: jenkins-install

.PHONY:install-latest
install-latest: jenkins-install-latest

.PHONY:jenkins-jobs
jenkins-jobs: $(JOBS:%=job-%)

.PHONY:$(JOBS:%=job-%)
$(JOBS:%=job-%):
	mkdir -p workdir/jobs/$(@:job-%=%)
	php -d include_path=$(TOOLSDIR)/php $(TOOLSDIR)/horde-components -T php-ci-tools/workspace/pear/pear -t $(WORKDIR)/templates -c workdir/jobs/$(@:job-%=%) --pearrc=$(TOOLSDIR)/../.pearrc $(HORDE_FRAMEWORK)/$(@:job-%=%)

PHONY:clean-all
clean-all: clean clean-jobs

PHONY:clean
clean: jenkins-clean

.PHONY:clean-jobs
clean-jobs: $(JOBS:%=clean-job-%)

.PHONY:$(JOBS:%=clean-job-%)
$(JOBS:%=clean-job-%):
	rm -rf workdir/jobs/$(@:clean-job-%=%)/builds
	rm -rf workdir/jobs/$(@:clean-job-%=%)/htmlreports
	rm -rf workdir/jobs/$(@:clean-job-%=%)/lastStable
	rm -rf workdir/jobs/$(@:clean-job-%=%)/lastSuccessful
	rm -rf workdir/jobs/$(@:clean-job-%=%)/nextBuildNumber
	rm -rf workdir/jobs/$(@:clean-job-%=%)/workspace
