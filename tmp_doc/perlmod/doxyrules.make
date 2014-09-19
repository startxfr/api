DOXY_EXEC_PATH = /var/www/html/startx/api/tmp_doc
DOXYFILE = /var/www/html/startx/api/tmp_doc/-
DOXYDOCS_PM = /var/www/html/startx/api/tmp_doc/perlmod/DoxyDocs.pm
DOXYSTRUCTURE_PM = /var/www/html/startx/api/tmp_doc/perlmod/DoxyStructure.pm
DOXYRULES = /var/www/html/startx/api/tmp_doc/perlmod/doxyrules.make

.PHONY: clean-perlmod
clean-perlmod::
	rm -f $(DOXYSTRUCTURE_PM) \
	$(DOXYDOCS_PM)

$(DOXYRULES) \
$(DOXYMAKEFILE) \
$(DOXYSTRUCTURE_PM) \
$(DOXYDOCS_PM): \
	$(DOXYFILE)
	cd $(DOXY_EXEC_PATH) ; doxygen "$<"
