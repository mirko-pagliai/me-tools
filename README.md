# MeTools #

## Installazione ##
Inserire MeTools nella directory Plugin.
Carica MeTools in bootstrap.php:
CakePlugin::load('MeTools');
Nella webroot della propria applicazione, creare un link (o copiare) alla webroot di MeTools:
cd webroot
ln -s ../Plugin/MeTools/webroot/ MeTools

### Librerie/script esterni ###
**meCms** usa diverse librerie o script esterni, solitamente localizzati tutti in *webroot*:
- JQuery 1.9.1 ([sito](http://jquery.com));
- Bootstrap 2.3.1 ([sito](http://twitter.github.com/bootstrap));