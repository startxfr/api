FROM startx/sv-php
MAINTAINER Christophe LARUE <dev@startx.fr>
ENV DOCROOT /var/www/html

COPY ./* $DOCROOT/
RUN chown apache:apache -R /var/www/html \
    && rm -rf $DOCROOT/_* $DOCROOT/.startx $DOCROOT/README.md $DOCROOT/Dockerfile $DOCROOT/.gitignore

CMD ["/usr/bin/echo","start data container"]
