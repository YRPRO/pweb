<?php 
//require('dataBase.php');
function Inscription ($login,$nom, $prenom,$sexe,$jour,$mois,$annee,$email,$pwd) {
		//require ("../modele/dataBase.php");
		global $db;
		$dateN= $annee.'-'.$mois.'-'.$annee; // On stock dans ce format parceque c pour le type date de la base de donnée 
		$req = $db->prepare('INSERT INTO utilisateur(login, nom, prenom, sexe, dateN,email, password, dateInscription) 
								VALUES(?,?, ?, ?, ?, ?, ?, CURDATE())');
		$req->execute([$login,$nom, $prenom,$sexe,$dateN,$email,$pwd]);
	}

	function recupCommentaireUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT c.idCommentaire,c.commentaire,c.dateCreation,t.libelleTheme,r.typeRestriction,c.login,c.nbLike,c.nbUnLike
							 FROM commentaire c,theme t , restriction r 
							 WHERE c.idTheme = t.idTheme 
						AND   c.idRestriction = r.idRestriction
						AND   c.login = ? 	
							');
		$q->execute([$login]);
		$dataCom = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();
		return $dataCom;
	}

	function recupCommentairePublic(){
		global $db;
		$q = $db->prepare('SELECT *
							 FROM commentaire c,theme t , restriction r 
							 WHERE c.idTheme = t.idTheme 
						AND   c.idRestriction = r.idRestriction
						AND   r.typeRestriction = ?
							');
		$q->execute(['public']);
		$dataCom = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();
		return $dataCom;
	}
	//fonction de recuperation des com public excepté pour un utilisateur donné
	function recupCommentairePublicSansUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT *
							 FROM commentaire c,theme t , restriction r 
							 WHERE c.idTheme = t.idTheme 
						AND   c.idRestriction = r.idRestriction
						AND   r.typeRestriction = ?
						AND c.login != ?
							');
		$q->execute(['public',$login]);
		$dataCom = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();
		return $dataCom;
	}
	function recupCommentairePublicTheme($theme){
		global $db;
		htmlspecialchars($theme);
		$idTheme = getThemeId($theme);
		$q = $db->prepare('SELECT c.commentaire as commentaire , c.dateCreation as dateCreation  , t.libelleTheme as libelleTheme
		 , r.typeRestriction as restriction, c.nbLike as nbLike,c.nbunLike as nbUnLike,c.login as login
							 FROM commentaire c,theme t , restriction r 
							 WHERE c.idTheme = t.idTheme 
						AND   c.idRestriction = r.idRestriction
						AND   r.typeRestriction = ?
						AND   c.idTheme = ?
							');
		//$q = $db->prepare('SELECT * FROM commentaire WHERE login = ?');
		$q->execute(['public',$idTheme]);
		$dataCom = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();
		return $dataCom;
	}
	
	
	//fonction de recuperation du meilleur commentaire (le plus liker)
	function recupMeilleurCom(){
		global $db;
		$q = $db->prepare('SELECT MAX(c.nbLike) ,c.commentaire , c.dateCreation , t.libelleTheme , r.typeRestriction , c.nbLike,c.nbunLike,c.login
							 FROM commentaire c,theme t , restriction r 
							 WHERE c.idTheme = t.idTheme 
							AND   c.idRestriction = r.idRestriction
							');
		$q->execute();
		$dataMeilleurcom = $q->fetchALL(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataMeilleurcom;
	}
	//fonction de recuperation du nombre d'utilisateur (de comptes)
	function recupNbUtilsateur(){
		global $db;
		$q = $db->prepare('SELECT COUNT(login)
							 FROM utilisateur
							');
		$q->execute();
		$tmp = $q->fetch();
		$dataNbUtilisateur = $tmp[0];
		$q->closeCursor();	
		return $dataNbUtilisateur;
	}

function recupLibelleTheme(){
		global $db;
		$q = $db->prepare('SELECT libelleTheme
							 FROM theme
							');
		$q->execute();
		$data = $q->fetchALL(PDO::FETCH_OBJ);
		//$dataNbUtilisateur = $tmp[0];
		$q->closeCursor();	
		return $data;
	}
	//fonction de recuperation du nombre de commentaires
	function recupNbcommentaire(){
		global $db;
		$q = $db->prepare('SELECT COUNT(idCommentaire)
							 FROM commentaire
							');
		$q->execute();
		$tmp = $q->fetch();
		$dataNbCom = $tmp[0];
		$q->closeCursor();	
		return $dataNbCom;
	}
	//fonction de recuperation du theme le plus utiliser
	function recupMeilleurTheme(){
		global $db;
		$q = $db->prepare('SELECT COUNT(c.idTheme),t.libelleTheme
							 FROM commentaire c,theme t
							 WHERE c.idTheme = t.idTheme
							 GROUP BY c.idTheme
							');
		$q->execute();
		$dataMeilleurTheme = $q->fetchAll(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $dataMeilleurTheme[0]->libelleTheme;
	}
	//fonction de recuperation de l'utilisateur le plus actif(qui à poster le plus de commentaires)
	function recupUtilisateurPlusActif(){
		global $db;
		$q = $db->prepare('SELECT COUNT(c.login), u.login
							 FROM commentaire c,utilisateur u
							 WHERE c.login = u.login
							 GROUP BY c.login
							');
		$q->execute();
		$dataUtilisateurPlusActif = $q->fetchAll(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataUtilisateurPlusActif[0]->login;
	}
	//fonction de recuperation de tout les utilisateur
	function recupListeUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT *
							 FROM utilisateur u
							 WHERE u.login != ?
							 
							');
		$q->execute([$login]);
		$data = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $data;
	}
	//fonction de recuperation de la restiction d'un commentaire
	function recupRestrictionCom($idCommentaire){
		global $db;
		$q = $db->prepare('SELECT r.typeRestriction
							 FROM commentaire c, restriction r
							 WHERE c.idRestriction = r.idRestriction 
							 AND c.idCommentaire = ? 
							  ');
		$q->execute([$idCommentaire]);
		$dataResCom = $q->fetchALL(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataResCom[0]->typeRestriction;
	}
	//fonction de recupation des restriction
	function recupRestriction(){
		global $db;
		$q = $db->prepare('SELECT r.typeRestriction
							 FROM  restriction r
							  ');
		$q->execute();
		$dataRestricion = $q->fetchALL(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $dataRestricion;
	}
	//fonction de recuperation des amis d'un utilisateur
	function recupAmisUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT c.amis
							 FROM contact c
							 WHERE c.utilisateur = ? 
							  ');
		$q->execute([$login]);
		$dataAmis = $q->fetchALL(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataAmis;
	}
	//fonction recuperation nb de like pour un com
	function recupNbLike($idCommentaire){
		global $db;
		$q = $db->prepare('SELECT c.nbLike
							 FROM commentaire c
							 WHERE c.idCommentaire = ?
							  ');
		$q->execute([$idCommentaire]);
		$tmp = $q->fetch();
		$dataNbLike = $tmp[0];
		$q->closeCursor();	
		return $dataNbLike;
	}
	//fonction recuperation nb de unlike pour un com
	function recupNbUnLike($idCommentaire){
		global $db;
		$q = $db->prepare('SELECT c.nbUnLike
							 FROM commentaire c
							 WHERE c.idCommentaire = ?
							  ');
		$q->execute([$idCommentaire]);
		$tmp = $q->fetch();
		$dataNbUnLike = $tmp[0];
		$q->closeCursor();	
		return $dataNbUnLike;
	}
	//fonction recuperation nb de theme
	function recupNbTheme(){
		global $db;
		$requete = 'SELECT COUNT(t.idTheme)
					 FROM theme t';
		$data = $db->query($requete);
		$tmp = $data->fetch();
		$dataNbTheme = $tmp[0];
		return $dataNbTheme;
	}
	//fonction ajout d'un like pour un commentaire
	function ajoutLike($login,$idCommentaire){
		global $db;
		$q = $db->prepare('UPDATE commentaire c
        					SET nbLike=nbLike+1
        					WHERE  c.idCommentaire = ? '
        					);
		$q->execute([$idCommentaire]);
		//ajout d'un enregistrement dans la table likeparutilisateur(pour les future restriction)
		ajoutBlocageLike($login,$idCommentaire);


		$q->closeCursor();	
	}
	//fonction ajout d'un unlike pour un commentaire
	function ajoutUnLike($login,$idCommentaire){
		global $db;
		$q = $db->prepare('UPDATE commentaire c
        					SET nbUnLike=nbUnLike+1
        					WHERE  c.idCommentaire = ? '
        					);
		$q->execute([$idCommentaire]);
		//ajout d'un enregistrement dans la table likeparutilisateur(pour les future restriction)
		ajoutBlocageLike($login,$idCommentaire);

		$q->closeCursor();	
	}
	//recuperation des info d'un utilisateur
	function recupInfoUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT *
							 FROM utilisateur u 
							 WHERE u.login = ?
							');
		$q->execute([$login]);
		$dataUtilisateur = $q->fetchALL(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataUtilisateur;
	}
	//recuperation des commentaire des amis d'un utilisateur
	function recupComAmis($login){
		global $db;
		$q = $db->prepare('SELECT *
							 FROM  commentaire c , restriction r , theme t
							 WHERE c.idRestriction = r.idRestriction
							 AND  c.idTheme = t.idTheme
							 AND 	r.typeRestriction = ? 
							 AND c.login IN (SELECT a.amis
							 					FROM contact a
							 					WHERE a.utilisateur = ?)
							');
		$q->execute(["contact",$login]);
		$dataComAmis = $q->fetchALL(PDO::FETCH_OBJ);

		$q->closeCursor();	
		return $dataComAmis;
	}
	//fonction permettant l'ajout d'un enregistrement dans la table like par utilisteur
	//pour qu'un utilisateur ne puisse pas liker plusieurs fois le meme commentaire
	function ajoutBlocageLike($login,$idCommentaire){
		global $db;
		$q = $db->prepare("INSERT INTO likeparutilisateur (idCommentaire,login) 
							VALUES (?,?)
        					");
		$q->execute([$idCommentaire,$login]);
		//$q->closeCursor();
	}
	//fonction de verification pour savoir si un utilisateur à déjà liker ou unliker un commentaire
	function aDejaLiker($login,$idCommentaire){
		global $db;
		$req = $db->prepare("SELECT l.idLikeParUtilisateur 	 
							 FROM likeparutilisateur l	 
							 WHERE l.login = ? AND l.idCommentaire = ?
							 ");
			$req->execute([$login,$idCommentaire]);		
			$reponse = $req->RowCount()	;
			//reponse en fonction du resultat de la requete au dessus
			if($reponse > 0)
				return true;
			else
				return false;
	}
	//fonction de modification d'un commentaire
	function modifCommentaire($idCommentaire,$nouvCom){
		global $db;
		//echappement html
		htmlspecialchars($nouvCom);
		//modification du commentaire dans la base 
		$q = $db->prepare('UPDATE commentaire c
        					SET c.commentaire = ?
        					WHERE c.idCommentaire = ? '
        					);
		$q->execute([$nouvCom,$idCommentaire]);
		$q->closeCursor();
	}
	//fonction de recuperation d'un id d'un theme par son libelle
	function getThemeId($libelle){
		htmlspecialchars($libelle);
		global $db;
		$q = $db->prepare("SELECT  t.idTheme 
							 FROM theme t
							 WHERE t.libelleTheme = ?
							 ");
		$q->execute([$libelle]);
		$data = $q->fetch();
		$id = $data[0];
		$q->closeCursor();	
		return $id;
	}
	//fonction de recuperation d'un id d'une restriction par son libelle
	function getRestrictionId($libelle){
		htmlspecialchars($libelle);
		global $db;
		$q = $db->prepare("SELECT  r.idRestriction 
							 FROM restriction r
							 WHERE r.typeRestriction = ?
							 ");
		$q->execute([$libelle]);
		$data = $q->fetch();
		$id = $data[0];
		$q->closeCursor();	
		return $id;
	}
	//fonction d'ajout de commentaire
	function ajoutCommentaire($login,$commentaire,$theme,$restriction){
		//ajouter date du jour avec  CURDATE()
		//echappement html
		htmlspecialchars($commentaire);
		htmlspecialchars($theme);
		htmlspecialchars($restriction);
		$idTheme = getThemeId($theme);
		$idRestriction = getRestrictionId($restriction);
		//insertion dans la base
		global $db;
		$q = $db->prepare("INSERT INTO commentaire (commentaire,dateCreation,idTheme,idRestriction,login,nbLike,nbUnlike) 
							VALUES (?,CURDATE(),?,?,?,0,0)
        					");
		$q->execute([$commentaire,$idTheme,$idRestriction,$login]);

	}
	//fonction de modification de la restriction d'un commentaire
	function modifRestrictionCommentaire($idCommentaire,$restriction){
		global $db;
		//echappement html
		htmlspecialchars($restriction);
		//recuperation de l'id de la restriction
		$id = getRestrictionId($restriction);
		//modification du commentaire dans la base 
		$q = $db->prepare('UPDATE commentaire c
        					SET c.idRestriction = ?
        					WHERE c.idCommentaire = ? '
        					);
		$q->execute([$id,$idCommentaire]);
		$q->closeCursor();
	}
	//fonction de recupation du nb d'amis pour un utilisateur
	function recupNbAmis($login){
		global $db;
		$q = $db->prepare ('SELECT c.utilisateur
					 FROM contact c
					 WHERE c.utilisateur = ?');
		$q->execute([$login]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		$nb = 0;
		for($i=0;$i<count($data);$i++){
			$nb++;
		}
		$q->closeCursor();	
		return $nb;
	}
	//fonction de recupation du nb de commentaire pour un utilisateur
	function recupNbComUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT c.idCommentaire
					 FROM commentaire c
					 WHERE c.login = ?');
		$q->execute([$login]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		$nb = 0;
		for($i=0;$i<count($data);$i++){
			$nb++;
		}
		$q->closeCursor();	
		return $nb;
	}
	//fonction de recupation du nb de like pour un utilisateur
	function recupNbLikeUtilisateur($login){
		global $db;
		$q = $db->prepare('SELECT c.nbLike
					 FROM commentaire c
					 WHERE c.login = ?');
		$q->execute([$login]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		//boucle pour recuperer le nb de like 
		$nb = 0;
		for($i=0;$i<count($data);$i++){
			$nb = $nb + intval($data[$i]->nbLike);
		}
		
		$q->closeCursor();	
		return $nb;
	}
	//fonction d'ajout du chemin de la photo de profil
	function ajoutCheminPhoto($login,$chemin){
		global $db;

			$q = $db->prepare("SELECT p.chemin
							 FROM photoprofil p
							 WHERE p.login = ?
							 ");
			$q->execute([$login]);
			$trouver = $q->RowCount();
			if($trouver){
				$q = $db->prepare('UPDATE photoprofil p
        					SET p.chemin = ?
        					WHERE  p.login = ? '
        					);
				$q->execute([$chemin,$login]);
			}
			else{
					$q = $db->prepare("INSERT INTO photoprofil (login,chemin) 
							VALUES (?,?)
        					");
					$q->execute([$login,$chemin]);	
			}
	}
	//fonction de recupeation du chemin de la photo de profil 
	function recupCheminPhotoProfil($login){
		global $db;
		$q = $db->prepare("SELECT p.chemin
							 FROM photoprofil p
							 WHERE p.login = ?
							 ");
		$q->execute([$login]);
		$data = $q->fetch();
		$chemin = $data[0];
		$q->closeCursor();	
		return $chemin;
	}
	//fonction permettant de savoir si un utilisateur dispose deja d'une photo
	function aDejaUnPhoto($login){
		global $db;
			$q = $db->prepare("SELECT p.chemin
							 FROM photoprofil p
							 WHERE p.login = ?
							 ");
			$q->execute([$login]);
			$trouver = $q->RowCount();
			if($trouver)
				return true;
			else
				return false;
	}
	//fonction de recuperation des messages d'un utilisateur
	function recupMessageUtilisateur($login){
		global $db;
		$q = $db->prepare("SELECT *
							 FROM message m
							 WHERE m.destinateur = ?
							 ");
		$q->execute([$login]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $data;
	}
	//fonction d'ajout d'un message privé dans la base de données
	function ajoutMessagePrive($message,$login,$destinateur){
		global $db;
		$q = $db->prepare("INSERT INTO message (message,expediteur,destinateur,dateCreation) 
							VALUES (?,?,?,CURDATE())
        					");
		$q->execute([$message,$login,$destinateur]);	
	}
	//fonction permettant de marquer un message comme etant lu
	function marquerMessageLu($idMessage){
		global $db;
		$lu = true;
		$q = $db->prepare('UPDATE message m
        					SET m.lu = ?
        					WHERE  m.idMessage = ? '
        					);
		$q->execute([$lu,$idMessage]);
	}
	//fonction permettant de repondre à un message
	function repondreMessage($idMessage,$reponse,$login,$destinateur){
		global $db;
		marquerMessageLu($idMessage);
		ajoutMessagePrive($reponse,$login,$destinateur);
	}
	//fonction de suppression d'un message privé
	function supprimerMessage($idMessage){
		global $db;
		$q = $db->prepare("DELETE FROM message 
							WHERE idMessage = ?
						");
		$q->execute([$idMessage]);
	}
	//fonction de recuperation des demande pour un utilisateur
	function recupDemandeUtilisateur($login){
		global $db;
		$q = $db->prepare("SELECT *
							 FROM demandeAmis d
							 WHERE d.destinateur = ?
							 ");
		$q->execute([$login]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $data;
	}
	//fonction ajoutant un nouvelle demande
	function nouvelleDemande($login,$destinateur){
		global $db;
		$q = $db->prepare("INSERT INTO demandeAmis (demandeur,destinateur) 
							VALUES (?,?)
        					");
		$q->execute([$login,$destinateur]);
	}
	//fonction d'ajout amis
	function ajoutAmis($login,$Amis){
		global $db;
		$q = $db->prepare("INSERT INTO contact (utilisateur,amis) 
							VALUES (?,?)
        					");
		$q->execute([$login,$Amis]);
	}
	//fonction de recup des info d'une demande
	function recupInfoDemandeAmis($idDemande){
		global $db;
		$q = $db->prepare("SELECT *
							 FROM demandeAmis d
							 WHERE d.idDemande = ?
							 ");
		$q->execute([$idDemande]);
		$data = $q->fetchAll(PDO::FETCH_OBJ);
		$q->closeCursor();	
		return $data;
	}
	
	//fonction permettant d'accepter une demande en amis
	function accepterDemandeAmis($idDemande){
		global $db;
		$donneDemande = recupInfoDemandeAmis($idDemande);
		$login = $donneDemande[0]->destinateur;
		$amis =$donneDemande[0]->demandeur;
		ajoutAmis($login,$amis);

	}
	//fonction de suppression d'une demande une fois celle ci accepter
	function supprimerDemandeAmis($idDemande){
		global $db;
		$q = $db->prepare("DELETE FROM demandeAmis 
							WHERE idDemande = ?
						");
		$q->execute([$idDemande]);
	}
?>