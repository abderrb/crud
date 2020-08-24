<?php

/**
 * Fonction qui formate une date donnée 
 *
 * @param [string] $origDate
 * @return string
 */
function formatDate($origDate)
{
    // On définit la langue du site
    setlocale(LC_TIME, 'FR_fr');

    // On formate la date dans la langue choisi
    $newDate = strftime('%A %e %B %Y -%T', strtotime($origDate));

    // On encore en UTF-8 pour gérer les caractères spéciaux
    $newDate = utf8_encode($newDate);

    // On retourne la date formatée 
    return $newDate;
}
/**
 * Cette fonction renvoie un extrait du texte raccourci à la longueur demandée
 *
 * @param string $texte
 * @param integer $longueur
 * @return string
 */
function extrait(string $texte, int $longueur): string
{

    // On décode les caractères HTML
    $texte = htmlspecialchars_decode($texte);

    // On supprime le HTML
    $texte = strip_tags($texte);

    // On raccourci le texte
    $texteReduit = mb_strimwidth($texte, 0, $longueur, '...');

    return $texteReduit;
}


/**
 * Cette fonction génère une miniature d'une image dans la taille demandée (carré) (PNG et JPG)
 * 
 *@param string $fichier Chemin complet du fichier
 *@param integer $taille Taille en pixels
 * @return boolean 
 */
function mini(string $fichier, int $taille): bool
{
    $dimensions = getimagesize($fichier);
    $decalageX = $decalageY = 0;

    switch ($dimensions[0] <=> $dimensions[1]) {
        case -1: // Portrait
            $tailleCarre = $dimensions[0];
            $decalageY = ($dimensions[1] - $tailleCarre) / 2;
            break;
        case 0: // Carré
            $tailleCarre = $dimensions[0];
            break;
        case 1: // Paysage
            $tailleCarre = $dimensions[1];
            $decalageX = ($dimensions[0] - $tailleCarre) / 2;
    }


    switch ($dimensions['mime']) {
        case 'image/png':
            $imageTemp = imagecreatefrompng($fichier);
            break;
        case 'image/jpeg':
            $imageTemp = imagecreatefromjpeg($fichier);
            break;
        default:
            return false;
    }


    $imageDest = imagecreatetruecolor($taille, $taille);

    imagecopyresampled(
        $imageDest, // Image destination
        $imageTemp, // Image source
        0,          // point gauche de la zone de collage
        0,          // point supérieur de  la zone de collage  
        $decalageX, // point gauche de la zone de copie
        $decalageY, // point supérieur de  la zone de copie
        $taille,        // largeur de la zone de collage
        $taille,        // hauteur de la zone de collage
        $tailleCarre, // largeur de la zone de copie
        $tailleCarre // hauteur de la zone de copie
    );

    // On "démonte" le nom de fichier
    $chemin = pathinfo($fichier, PATHINFO_DIRNAME);
    $nomFichier = pathinfo($fichier, PATHINFO_FILENAME);
    $extension = pathinfo($fichier, PATHINFO_EXTENSION);

    $nouveauFichier = "$chemin/$nomFichier-{$taille}x$taille.$extension";

    switch ($dimensions['mime']) {
        case 'image/png':
            imagepng($imageDest, $nouveauFichier);
            break;
        case 'image/jpeg':
            imagejpeg($imageDest, $nouveauFichier);
    }
    // On détruit les images en mémoire
    imagedestroy($imageTemp);
    imagedestroy($imageDest);

    return true;
}
