include settings.mk
include jenkins/jenkins.mk

HORDE_APPS=workdir/jobs/horde-git/workspace
HORDE_FRAMEWORK=$(HORDE_APPS)/framework
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
     Feed \
     Group \
     History \
     Http \
     Imap_Client \
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
     Notification \
     Pdf \
     Pear \
     Perms \
     Prefs \
     PubSub \
     Push \
     Rdo \
     Release \
     Routes \
     Secret \
     Service_Gravatar \
     Serialize \
     Share \
     Stream_Filter \
     Support \
     Text_Diff \
     Text_Filter \
     Text_Flowed \
     Text_Textile \
     Token \
     Translation \
     Url \
     Util \
     Vfs \
     View \
     Xml_Element \
     Xml_Wbxml \
     Yaml

APP_JOBS=mnemo

.PHONY:install
install: jenkins-install

.PHONY:install-latest
install-latest: jenkins-install-latest

.PHONY:jenkins-jobs
jenkins-jobs: $(JOBS:%=job-%) $(APP_JOBS:%=job-%)

.PHONY:$(JOBS:%=job-%)
$(JOBS:%=job-%):
	mkdir -p workdir/jobs/$(@:job-%=%)
	php -d include_path=$(TOOLSDIR)/php $(TOOLSDIR)/horde-components -T php-ci-tools/workspace/pear/pear -t $(WORKDIR)/templates/config --cisetup=workdir/jobs/$(@:job-%=%) --pearrc=$(TOOLSDIR)/../.pearrc $(HORDE_FRAMEWORK)/$(@:job-%=%)

.PHONY:$(APP_JOBS:%=job-%)
$(APP_JOBS:%=job-%):
	mkdir -p workdir/jobs/$(@:job-%=%)
	php -d include_path=$(TOOLSDIR)/php $(TOOLSDIR)/horde-components -T php-ci-tools/workspace/pear/pear -t $(WORKDIR)/templates/config --cisetup=workdir/jobs/$(@:job-%=%) --pearrc=$(TOOLSDIR)/../.pearrc $(HORDE_APPS)/$(@:job-%=%)

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
