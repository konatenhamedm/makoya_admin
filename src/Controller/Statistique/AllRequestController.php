<?php

namespace App\Controller\Statistique;

use App\Controller\BaseController;
use App\Repository\CategorieRepository;
use App\Repository\NombreClickRepository;
use App\Repository\NoteRepository;
use App\Repository\PrestataireRepository;
use App\Repository\PrestataireServiceRepository;
use App\Repository\ServicePrestataireRepository;
use App\Repository\SousCategorieRepository;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/ads/statistque/requests')]
class AllRequestController extends BaseController
{
    #[Route('/api/diagramme/categorie', name: 'app_statistique_categorie_requete', condition: "request.query.has('filters')")]
    public function apiDiagrammeCategories(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        CategorieRepository $categorieRepository,
        NombreClickRepository $nombreClickRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $dateDebut = $filters['dateDebut'];
        $dateFin = $filters['dateFin'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $categories = $categorieRepository->findAll();

        $data = $prestataireServiceRepository->getCategorieByNombrePrestataire($dateDebut, $dateFin);

        $dataNombreVue = $nombreClickRepository->getCategorieByNombreVue($dateDebut, $dateFin);
        //dd($data);

        foreach ($categories as $key1 => $categorie) {

            foreach ($data as $key => $elt) {
                if ($elt['categorie'] == $categorie->getLibelle()) {

                    $dataArrayPrestataire[$elt['_total']] =
                        [$elt['categorie'], $elt['_total']];
                }
            }
            foreach ($dataNombreVue as $key => $elt) {
                if ($elt['categorie'] == $categorie->getLibelle()) {

                    $dataArrayVue[$elt['_total']] =
                        [$elt['categorie'], intval($elt['_total'])];
                }
            }
        }

        $i = 0;
        $j = 0;
        foreach ($categories as $key => $value) {


            if (!in_array($value->getLibelle(), $dataArrayPrestataire)) {

                $dataArrayPrestataire[$i] =
                    [$value->getLibelle(), 0];
            }
            if (!in_array($value->getLibelle(), $dataArrayVue)) {

                $dataArrayVue[$j] =
                    [$value->getLibelle(), 0];
            }

            $i--;
            $j--;
        }


        /*     krsort($dataArray);
        ksort($dataArray); */
        krsort($dataArrayPrestataire);
        krsort($dataArrayVue);

        foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        }
        foreach ($dataArrayVue as $key => $value) {

            $dataArrayVueFinal[] = $value;
        }
        $series =  [
            'prestataire' => $dataArrayPrestataireFinal,
            'vue' => $dataArrayVueFinal
        ];

        //dd(count($dataArrayPrestataireFinal));

        return $this->json(['series' => $series]);
    }

    #[Route('/api/diagramme/souscategorie', name: 'app_statistique_sous_categorie_requete', condition: "request.query.has('filters')")]
    public function apiDiagrammeSousCategories(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        SousCategorieRepository $categorieRepository,
        NombreClickRepository $nombreClickRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $dateDebut = $filters['dateDebut'];
        $dateFin = $filters['dateFin'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $categories = $categorieRepository->findAll();

        $data = $prestataireServiceRepository->getSousCategorieByNombrePrestataire($dateDebut, $dateFin);

        $dataNombreVue = $nombreClickRepository->getSousCategorieByNombreVue($dateDebut, $dateFin);
        //dd($dataNombreVue);

        foreach ($categories as $key1 => $categorie) {

            foreach ($data as $key => $elt) {
                if ($elt['categorie'] == $categorie->getLibelle()) {

                    $dataArrayPrestataire[$elt['_total']] =
                        [$elt['categorie'], $elt['_total']];
                }
            }
            foreach ($dataNombreVue as $key => $elt) {
                if ($elt['categorie'] == $categorie->getLibelle()) {

                    $dataArrayVue[$elt['_total']] =
                        [$elt['categorie'], intval($elt['_total'])];
                }
            }
        }

        $i = 0;
        foreach ($categories as $key => $value) {


            if (!in_array($value->getLibelle(), $dataArrayPrestataire)) {

                $dataArrayPrestataire[$i] =
                    [$value->getLibelle(), 0];
            }
            if (!in_array($value->getLibelle(), $dataArrayVue)) {

                $dataArrayVue[$i] =
                    [$value->getLibelle(), 0];
            }

            $i--;
        }
        // dd($dataArrayPrestataire);

        /*     krsort($dataArray);
        ksort($dataArray); */
        krsort($dataArrayPrestataire);
        krsort($dataArrayVue);

        foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        }
        foreach ($dataArrayVue as $key => $value) {

            $dataArrayVueFinal[] = $value;
        }

        $series =  [
            'vue' => $dataArrayVueFinal,
            'prestataire' => $dataArrayPrestataireFinal
        ];

        return $this->json(['series' => $series]);
    }
    #[Route('/api/diagramme/service', name: 'app_statistique_service_requete', condition: "request.query.has('filters')")]
    public function apiDiagrammeServices(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        NombreClickRepository $nombreClickRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $dateDebut = $filters['dateDebut'];
        $dateFin = $filters['dateFin'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $services = $servicePrestataireRepository->findAll();

        //dd($services);

        $data = $prestataireServiceRepository->getServiceByNombrePrestataire($dateDebut, $dateFin);

        $dataNombreVue = $nombreClickRepository->getServiceByNombreVue($dateDebut, $dateFin);
        // dd($dataNombreVue);

        foreach ($services as $key1 => $service) {

            foreach ($data as $key => $elt) {
                if ($elt['service'] == $service->getLibelle()) {

                    $dataArrayPrestataire[$elt['_total']] =
                        [$elt['service'], $elt['_total']];
                }
            }
            foreach ($dataNombreVue as $key => $elt) {
                if ($elt['service'] == $service->getLibelle()) {

                    $dataArrayVue[$elt['_total']] =
                        [$elt['service'], intval($elt['_total'])];
                }
            }
        }

        $i = 0;
        foreach ($services as $key => $value) {


            if (!in_array($value->getLibelle(), $dataArrayPrestataire)) {

                $dataArrayPrestataire[$i] =
                    [$value->getLibelle(), 0];
            }
            if (!in_array($value->getLibelle(), $dataArrayVue)) {

                $dataArrayVue[$i] =
                    [$value->getLibelle(), 0];
            }

            $i--;
        }
        // dd($dataArrayPrestataire);

        /*     krsort($dataArray);
        ksort($dataArray); */
        krsort($dataArrayPrestataire);
        krsort($dataArrayVue);

        foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        }
        foreach ($dataArrayVue as $key => $value) {

            $dataArrayVueFinal[] = $value;
        }

        $series =  [
            'vue' => $dataArrayVueFinal,
            'prestataire' => $dataArrayPrestataireFinal
        ];

        return $this->json(['series' => $series]);
    }


    #[Route('/api/diagramme/effectif/localite/categorie', name: 'app_statistique_effectif_localite_categorie_requete', condition: "request.query.has('filters')")]
    public function apiEffectifByLocaliteAndCategorie(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        CategorieRepository $categorieRepository,
        NombreClickRepository $nombreClickRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $dateDebut = $filters['dateDebut'];
        $dateFin = $filters['dateFin'];
        $localite = $filters['localite'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $categories = $categorieRepository->findAll();

        $data = $prestataireServiceRepository->getEffectifByLocaliteAndCategorie($dateDebut, $dateFin, $localite);
        // dd($data);


        foreach ($categories as $key1 => $categorie) {

            foreach ($data as $key => $elt) {
                if ($elt['categorie'] == $categorie->getLibelle()) {
                    $dataArrayPrestataire[$elt['_total']] =
                        [$elt['categorie'], $elt['_total']];
                }
            }
        }

        $i = 0;

        foreach ($categories as $key => $value) {

            if (!in_array($value->getLibelle(), $dataArrayPrestataire)) {

                $dataArrayPrestataire[$i] =
                    [$value->getLibelle(), 0];
            }


            $i--;
        }


        /*     krsort($dataArray);
        ksort($dataArray); */
        krsort($dataArrayPrestataire);


        foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        }

        /*  $series =  [
            'prestataire' => $dataArrayPrestataireFinal,
            'vue' => $dataArrayVueFinal
        ]; */

        //dd(count($dataArrayPrestataireFinal));

        return $this->json(['series' => $dataArrayPrestataireFinal]);
    }
    #[Route('/api/classement/entreprise/par/localite/categorie', name: 'app_statistique_classement_entreprise_categorie_requete', condition: "request.query.has('filters')")]
    public function indexClassementEntreprieParLocaliteCategorie(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        PrestataireRepository $prestataireRepository,
        NombreClickRepository $nombreClickRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $localite = $filters['localite'];
        $categorie = $filters['categorie'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $prestataires = $prestataireRepository->findAll();

        $data = $nombreClickRepository->getClassementEntreprise($localite, $categorie);



        foreach ($prestataires as $key1 => $categorie) {

            foreach ($data as $key => $elt) {
                if ($elt['fournisseur'] == $categorie->getDenominationSociale()) {
                    $dataArrayPrestataire[$elt['_total']] =
                        [$elt['fournisseur'], intval($elt['_total'])];
                }
            }
        }

        $i = 0;

        foreach ($prestataires as $key => $value) {

            if (!in_array($value->getDenominationSociale(), $dataArrayPrestataire)) {

                $dataArrayPrestataire[$i] =
                    [$value->getDenominationSociale(), 0];
            }


            $i--;
        }


        /*     krsort($dataArray);
        ksort($dataArray); */
        krsort($dataArrayPrestataire);


        foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        }

        /*  $series =  [
            'prestataire' => $dataArrayPrestataireFinal,
            'vue' => $dataArrayVueFinal
        ]; */

        //dd(count($dataArrayPrestataireFinal));

        return $this->json(['series' => $dataArrayPrestataireFinal]);
    }



    #[Route('/api/taux/couverture/categorie', name: 'app_statistique_taux_categorie_categorie_requete', condition: "request.query.has('filters')")]
    public function indexTauxCouvertureCategoire(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        PrestataireRepository $prestataireRepository,
        CategorieRepository $categorieRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        $localite = $filters['localite'];
        $dateDebut = $filters['dateDebut'];
        $dateFin = $filters['dateFin'];

        if ($localite == null) {
            $localite = 8;
        }

        $dataArrayPrestataire = [];



        $categories = $categorieRepository->findAll();

        $data = $prestataireServiceRepository->getTauxCategorie($localite, $dateDebut, $dateFin);

        //dd($data);
        if (count($data) > 0) {
            foreach ($categories as $key1 => $categorie) {

                foreach ($data as $key => $elt) {

                    if ($elt['categorie'] == $categorie->getLibelle()) {

                        if ($key == 0) {
                            $dataArrayPrestataire[] = [
                                'name' => $elt['categorie'],
                                'y' => $elt['_total'],
                                'sliced' => true,
                                'selected' => true,

                            ];
                        } else {
                            $dataArrayPrestataire[] = [
                                'name' => $elt['categorie'],
                                'y' => $elt['_total']

                            ];
                        }
                    }
                }
            }
        }


        if (count($dataArrayPrestataire) > 0) {
            foreach ($categories as $key => $value) {

                if (!in_array($value->getLibelle(), $dataArrayPrestataire)) {

                    $dataArrayPrestataire[] = [
                        'name' => $value->getLibelle(),
                        'y' => 0

                    ];
                }
            }
        }

        return $this->json(['series' => $dataArrayPrestataire]);
    }


    #[Route('/api/note/entreprise/par/localite/categorie', name: 'app_statistique_note_entreprise_categorie_requete', condition: "request.query.has('filters')")]
    public function indexNoteEntreprieParLocaliteCategorie(
        Request $request,
        PrestataireServiceRepository $prestataireServiceRepository,
        PrestataireRepository $prestataireRepository,
        NoteRepository $noteRepository
    ) {
        $all = $request->query->all();
        $filters = $all['filters'] ?? [];
        // $dateDebut = $filters['dateDebut'];
        $localite = $filters['localite'];
        $categorie = $filters['categorie'];

        $dataArrayPrestataire = [];
        $dataArrayPrestataireFinal = [];

        $dataArrayVue = [];
        $dataArrayVueFinal = [];

        $prestataires = $prestataireRepository->findAll();

        $data = $noteRepository->getAvisEntreprises($localite, $categorie);

        //dd($data);


        // foreach ($prestataires as $key1 => $categorie) {

        foreach ($data as $key => $elt) {
            //  if ($elt['fournisseur'] == $categorie->getDenominationSociale()) {
            $dataArrayPrestataire[] =
                [$elt['fournisseur'], round(intval($elt['_total']), 2)];
            // }
        }
        // }


        // dd($dataArrayPrestataire);
        /*  dd($dataArrayPrestataire); */

        /*     krsort($dataArray);
        ksort($dataArray); */
        //krsort($dataArrayPrestataire);

        /*      dd($dataArrayPrestataire); */


        /* foreach ($dataArrayPrestataire as $key => $value) {

            $dataArrayPrestataireFinal[] = $value;
        } */


        /*  $series =  [
            'prestataire' => $dataArrayPrestataireFinal,
            'vue' => $dataArrayVueFinal
        ]; */

        //dd(count($dataArrayPrestataireFinal));

        return $this->json(['series' => $dataArrayPrestataire]);
    }
}
