.PHONY:install
install: hudson-war hudson-plugins

.PHONY:hudson-war
hudson-war: war/hudson.war
	mkdir -p war
	cd war && wget http://hudson-ci.org/latest/hudson.war

.PHONY:hudson-plugins
hudson-plugins:
	mkdir -p workdir/plugins
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151982/analysis-collector.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152325/analysis-core.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151962/checkstyle.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151372/clover.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152172/dashboard-view.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151963/dry.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152113/git.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/147476/greenballs.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/150456/htmlpublisher.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/143113/jdepend.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151965/pmd.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/151939/violations.hpi
	cd workdir/plugins && wget https://hudson.dev.java.net/files/documents/2402/152453/xunit.hpi
