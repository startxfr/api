

sxa2: projet sablier
==================

Interface de recherche d'un calendrier de formation avec les options suivantes :
* Interface Javascript (jquery) de réservation des formations
  * Vue sous forme de calendrier ( par mois, trimestre, année)
  * Flèches  de défilement avant-arrière sur le calendrier
  * Filtrage / recherche des évènements selon :
     * Code formation
     * Cursus formation
     * Exam / Pas Exam
     * Emplacement
     * Date début / fin
     * mots clefs
  * Fenêtre de détail lors d'un clic sur une date de formation + bouton de reservation
  * Formulaire de réservation avec :
     * Nom, prénom (*)
     * Société
     * adresse , cp - ville
     * tel (*)
     * mail (*)
     * formation + date souhaité
     * commentaire
     * comment connu le site
  * Vérification de la saisie des champs + validité du mail

Le formulaire enregistre une entrée dans un webservice [SXAPI](https://github.com/startxfr/sxapi/wiki) qui devra faire l'enregistrement dans une collection nosql + envoi de mail d'alert. Tout cela sera configuré dans une  [resource SXAPI](https://github.com/startxfr/sxapi/wiki/Resources) et exposée dans un [document d'API](https://github.com/startxfr/sxapi/wiki/API-Document) (terminologie SXAPI ;)


En option: developper une interface de consultation des reservations dans l'application [SXAPI-m](https://github.com/startxfr/sxapi-m)
