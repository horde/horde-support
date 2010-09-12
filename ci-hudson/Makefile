include settings.mk

HORDE_FRAMEWORK=workdir/jobs/horde/workspace/framework
TOOLSDIR=workdir/jobs/php-hudson-tools/workspace/pear/pear

URL_WAR=http://hudson-ci.org/latest/hudson.war
PLUGINS=analysis-collector.hpi \
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

JOBS=Autoloader \
     Controller \
     Date \
     Itip \
     Kolab_Config \
     Kolab_Session \
     Url

.PHONY:install
install: hudson-war hudson-plugins hudson-jobs

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

workdir/plugins/.keep:
	mkdir -p workdir/plugins
	touch $@

analysis-collector.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151982/analysis-collector.hpi

analysis-core.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152325/analysis-core.hpi

checkstyle.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151962/checkstyle.hpi

clover.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151372/clover.hpi

dashboard-view.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152172/dashboard-view.hpi

dry.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151963/dry.hpi

git.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152113/git.hpi

greenballs.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/147476/greenballs.hpi

htmlpublisher.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/150456/htmlpublisher.hpi

jdepend.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/143113/jdepend.hpi

pmd.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151965/pmd.hpi

violations.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151939/violations.hpi

xunit.hpi: workdir/plugins/.keep
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152453/xunit.hpi

.PHONY:$(JOBS:%=job-%)
$(JOBS:%=job-%):
	mkdir -p workdir/jobs/$(@:job-%=%)
	php -d include_path=$(TOOLSDIR)/php $(TOOLSDIR)/horde-components -c workdir/jobs/$(@:job-%=%) $(HORDE_FRAMEWORK)/$(@:job-%=%)

.PHONY:start
start:
	export SUBDIR=$(SUBDIR) && init.d/hudson start
