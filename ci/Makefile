include settings.mk
include jenkins/jenkins.mk

HORDE_FRAMEWORK=workdir/jobs/horde-git/workspace/framework
TOOLSDIR=workdir/jobs/php-ci-tools/workspace/pear/pear

JOBS=ActiveSync \
     Alarm \
     Argv \
     Auth \
     Autoloader \
     Cli_Modular \
     Compress \
     Constraint \
     Controller \
     Core \
     Crypt \
     Date \
     Date_Parser \
     Db \
     Exception \
     Group \
     History \
     Http \
     ImapClient \
     Injector \
     Itip \
     Kolab_Config \
     Kolab_Format \
     Kolab_Session \
     Kolab_Storage \
     Ldap \
     Log \
     LoginTasks \
     Mail \
     Mime \
     Pdf \
     Pear \
     Perms \
     Prefs \
     PubSub \
     Release \
     Secret \
     Share \
     Stream_Filter \
     Support \
     Text_Diff \
     Text_Flowed \
     Token \
     Translation \
     Url \
     Util \
     View \
     Xml_Element \
     Yaml
#    Feed \

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
	cp templates/job-gitignore workdir/jobs/$(@:job-%=%)/.gitignore

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
