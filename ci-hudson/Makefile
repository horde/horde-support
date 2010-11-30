include settings.mk

HORDE_FRAMEWORK=workdir/jobs/horde/workspace/framework
TOOLSDIR=workdir/jobs/php-hudson-tools/workspace/pear/pear

URL_WAR=http://updates.hudson-labs.org/download/war/1.384/hudson.war
PLUGINS=analysis-collector.hpi.1.8 \
        analysis-core.hpi.1.14 \
        checkstyle.hpi.3.10 \
        clover.hpi.3.0.2 \
        dashboard-view.hpi.1.8.1 \
        dry.hpi.2.10 \
        git.hpi.1.1 \
        greenballs.hpi.1.8 \
        htmlpublisher.hpi.0.4 \
        jdepend.hpi.1.2.2 \
        pmd.hpi.3.10 \
        violations.hpi.0.7.7 \
        xunit.hpi.1.12

PLUGINS_LATEST=analysis-collector.hpi \
               analysis-core.hpi \
               checkstyle.hpi \
               clover.hpi \
               dashboard-view.hpi \
               dry.hpi \
               git.hpi \
               greenballs.hpi \
               htmlpublisher.hpi \
               jdepend.hpi \
               pmd.hpi \
               violations.hpi \
               xunit.hpi
URL_WAR_LATEST=http://hudson-ci.org/latest/hudson.war


JOBS=Autoloader \
     Argv \
     Constraint \
     Controller \
     Date \
     Date_Parser \
     Db \
     Exception \
     Http \
     Injector \
     Itip \
     Kolab_Config \
     Kolab_Session \
     Ldap \
     Log \
     LoginTasks \
     Mail \
     Mime \
     Secret \
     Stream_Filter \
     Support \
     Text_Flowed \
     Token \
     Translation \
     Url \
     Xml_Element \
     Yaml

.PHONY:install
install: hudson-war hudson-plugins

.PHONY:install-latest
install-latest: hudson-war-latest hudson-plugins-latest

.PHONY:hudson-war
hudson-war: war/hudson.war

.PHONY:hudson-plugins
hudson-plugins: workdir/plugins/.keep $(PLUGINS)

.PHONY:hudson-jobs
hudson-jobs: $(JOBS:%=job-%)

war/.keep:
	mkdir -p war
	touch $@

war/hudson.war: war/.keep
	cd war && wget $(URL_WAR)

PHONY:hudson-war-latest
hudson-war-latest: war/.keep
	cd war && wget $(URL_WAR_LATEST)

workdir/plugins/.keep:
	mkdir -p workdir/plugins
	touch $@

PHONY: hudson-plugins-latest
hudson-plugins-latest: $(PLUGINS_LATEST)

PHONY:$(PLUGINS_LATEST)
$(PLUGINS_LATEST): workdir/plugins/.keep
	cd workdir/plugins && wget http://hudson-ci.org/latest/$(@)

PHONY: $(PLUGINS)
$(PLUGINS): workdir/plugins/.keep
	NAME=$(shell echo $(@) | sed -e 's/\.hpi\..*//'); \
	  VERSION=$(shell echo $(@) | sed -e 's/.*\.hpi\.//'); \
	  cd workdir/plugins && wget http://hudson-ci.org/download/plugins/$$NAME/$$VERSION/$$NAME.hpi

.PHONY:$(JOBS:%=job-%)
$(JOBS:%=job-%):
	mkdir -p workdir/jobs/$(@:job-%=%)
	php -d include_path=$(TOOLSDIR)/php $(TOOLSDIR)/horde-components -T php-hudson-tools/workspace/pear/pear -t $(SUBDIR)/templates -c workdir/jobs/$(@:job-%=%) --pearrc=$(TOOLSDIR)/../.pearrc $(HORDE_FRAMEWORK)/$(@:job-%=%)

.PHONY:start
start:
	export SUBDIR=$(SUBDIR) && init.d/hudson start

.PHONY:stop
stop:
	export SUBDIR=$(SUBDIR) && init.d/hudson stop

PHONY:clean-all
clean-all: clean clean-jobs

PHONY:clean
clean:
	rm -f log/hudson.log
	rm -f run/hudson.pid
	rm -rf run/hudson
	rm -f war/hudson.war war/hudson.tmp war/hudson.bak
	rm -rf workdir/plugins

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
