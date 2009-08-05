 			xajax.debug.text = [];
      xajax.debug.text[100] = 'ATTENTION : ';
      xajax.debug.text[101] = 'ERREUR : ';
      xajax.debug.text[102] = 'MESSAGE DE DEBUG XAJAX :\n';
      xajax.debug.text[103] = '...\n[RÉPONSE LONGUE]\n...';
      xajax.debug.text[104] = 'ENVOI DE LA REQUÊTE';
      xajax.debug.text[105] = 'ENVOYÉ [';
      xajax.debug.text[106] = ' octets]';
      xajax.debug.text[107] = 'APPEL : ';
      xajax.debug.text[108] = 'URI : ';
      xajax.debug.text[109] = 'INITIALISATION DE LA REQUÊTE';
      xajax.debug.text[110] = 'TRAITEMENT DES PARAMÈTRES [';
      xajax.debug.text[111] = ']';
      xajax.debug.text[112] = 'AUCUN PARAMÈTRE À TRAITER';
      xajax.debug.text[113] = 'PRÉPARATION DE LA REQUÊTE';
      xajax.debug.text[114] = 'DÉBUT DE L\'APPEL XAJAX (déprécié: utilisez plutôt xajax.request)';
      xajax.debug.text[115] = 'DÉBUT DE LA REQUÊTE';
      xajax.debug.text[116] = 'Aucun traitement disponible pour traiter la réponse du serveur.\n';
      xajax.debug.text[117] = '.\nVérifie s\'il existe des messages d\'erreur du serveur.';
      xajax.debug.text[118] = 'REÇUS [statut : ';
      xajax.debug.text[119] = ', taille: ';
      xajax.debug.text[120] = ' octets, temps: ';
      xajax.debug.text[121] = 'ms] :\n';
      xajax.debug.text[122] = 'Le serveur a retourné la statut HTTP suivant : ';
      xajax.debug.text[123] = '\nREÇUS :\n';
      xajax.debug.text[124] = 'Le serveur a indiqué une redirection vers :<br />';
      xajax.debug.text[125] = 'FAIT [';
      xajax.debug.text[126] = 'ms]';
      xajax.debug.text[127] = 'INITIALISATION DE L\'OBJET REQUÊTE';
       
      xajax.debug.exceptions = [];
      xajax.debug.exceptions[10001] = 'Réponse XML non valide : La réponse contient une balise inconnue : {data}.';
      xajax.debug.exceptions[10002] = 'GetRequestObject : XMLHttpRequest n\'est pas disponible, xajax est désactivé.';
      xajax.debug.exceptions[10003] = 'File pleine : Ne peut ajouter un objet à la file car elle est pleine.';
      xajax.debug.exceptions[10004] = 'Réponse XML non valide : La réponse contient une balise ou un texte inattendu : {data}.';
      xajax.debug.exceptions[10005] = 'URI de la requête non valide : URI non valide ou manquante; auto-détection échouée; veuillez en spécifier une explicitement.';
      xajax.debug.exceptions[10006] = 'Réponse de commande invalide : Commande de réponse reçue mal formée.';
      xajax.debug.exceptions[10007] = 'Réponse de commande invalide : Commande [{data}] est inconnue.';
      xajax.debug.exceptions[10008] = 'L\'élément d\'ID [{data}] est introuvable dans le document.';
      xajax.debug.exceptions[10009] = 'Requête invalide : Aucun nom de fonction indiqué en paramètre.';
      xajax.debug.exceptions[10010] = 'Requête invalide : Aucun objet indiqué en paramètre pour la fonction.';
       
      if ('undefined' != typeof xajax.config) {
        if ('undefined' != typeof xajax.config.status) {
          /*
            Object: mise à jour
          */
          xajax.config.status.update = function() {
            return {
              onRequest: function() {
                window.status = 'Envoi de la requête...';
              },
              onWaiting: function() {
                window.status = 'Attente de la réponse...';
              },
              onProcessing: function() {
                window.status = 'En cours de traitement...';
              },
              onComplete: function() {
                window.status = 'Fait.';
              }
            }
          }
        }
      }