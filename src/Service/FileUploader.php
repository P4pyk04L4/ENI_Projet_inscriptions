<?php

namespace App\Service;

use App\Entity\PhotoProfil;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private SluggerInterface $slugger;
    private string $dossierPhotos;
    private string $dossierPhotosAffichage;

    public function __construct(SluggerInterface $slugger, string $dossierPhotos, string $dossierPhotosAffichage){
        $this->slugger = $slugger;
        $this->dossierPhotos = $dossierPhotos;
        $this->dossierPhotosAffichage = $dossierPhotosAffichage;
    }
    public function checkAndUpload(UploadedFile $fichierPhoto){
        $nomOriginalFichier = pathinfo($fichierPhoto->getClientOriginalName(), PATHINFO_FILENAME);
        $nomSecuriseFichier = $this->slugger->slug($nomOriginalFichier);

        $photoAEnvoyer = new PhotoProfil();
        $photoAEnvoyer->setDateUpload(new \DateTime('now'))
            ->setNom($nomSecuriseFichier)
            ->setCheminAcces($fichierPhoto->getRealPath());

        try {
            // PREPARATION DU CHEMIN
            $dossierPhotosProfil = 'photosProfils';
            $dossierPhotos = $this->dossierPhotos . $dossierPhotosProfil;

            try {
                // CREATION DU DOSSIER SI INEXISTANT
                if (file_exists($dossierPhotos)) {
                    dump($dossierPhotos);
                } else {
                    mkdir($dossierPhotos, 0777, true);
                }
            } catch (\Exception $e) {
                dump("Erreur lors de la création du dossier : " . $e->getMessage());
            }

            // DEPLACEMENT DU FICHIER DANS LE DOSSIER
            $nomFinalFichier = $photoAEnvoyer->getNom() . '.' . $fichierPhoto->guessExtension();
            $fichierPhoto->move($dossierPhotos, $nomFinalFichier);

        } catch (FileException $e) {
            dump("Erreur lors du transfert du fichier : " . $e->getMessage());
        }

        $photoAEnvoyer->setCheminAcces($this->dossierPhotosAffichage .
            $dossierPhotosProfil . '/' . $nomFinalFichier);

        return $photoAEnvoyer;
    }
}