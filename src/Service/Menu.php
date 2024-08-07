<?php

namespace App\Service;

use App\Entity\ModuleGroupePermition;
use App\Entity\ConfigApp;
use App\Entity\Groupe;
use App\Entity\NombreClick;
use App\Entity\Prestataire;
use App\Entity\Publicite;
use App\Entity\PubliciteCategorie;
use App\Entity\PubliciteRegion;
use App\Entity\UserFront;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

use Psr\Container\ContainerInterface;
use function PHPUnit\Framework\isEmpty;

class Menu
{

    private $em;
    private $route;
    private $container;
    private $security;

    private $resp;
    private $tableau = [];


    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router, Security $security)
    {
        $this->em = $em;
        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
            $this->container = $router->getRouteCollection()->all();
            $this->security = $security;
        }
        //foreach($this->container as $key => $value){

        //  if(str_contains($key,'index')){
        //   $this->tableau [] = [
        // $key => str_replace('_',' ',$key)
        //  ];
        //}

        //  }

        // dd( $this->tableau);
        // if($this->getPermission() == null){
        // dd($this->getPermission());
        // }
        //dd($this->getPermission());
        /* if(!$this->getPermission()){
            dd("rrrr");
        }*/
        //$this->getPermission();
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function getNamePrestataire($reference)
    {
        return $this->em->getRepository(UserFront::class)->findOneBy(['reference' => $reference]);
    }
    public function listeModule()
    {

        return $this->em->getRepository(ModuleGroupePermition::class)->afficheModule($this->security->getUser()->getGroupe()->getId());;
    }



    public function listeGroupeModule()
    {


        return $this->em->getRepository(ModuleGroupePermition::class)->affiche($this->security->getUser()->getGroupe()->getId());;
    }

    public function findParametre()
    {

        return $this->em->getRepository(ConfigApp::class)->findConfig();
    }
    public function getTest()
    {
        return "#DDAD59";
    }
    public function getPermission()
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($this->security->getUser()->getGroupe()->getId(), $this->route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }


    public function getPubTypeLibelle($type, $id)
    {


        if ($type == "CAT") {
            return $this->em->getRepository(Publicite::class)->findOneBy(array('code' => $id))->getCategorie()->getLibelle();
        } elseif ($type == "ENC") {
            return "Encart";
        } elseif ($type == "RGP") {
            return $this->em->getRepository(Publicite::class)->findOneBy(array('code' => $id))->getRegion()->getNom();
        }
    }
    public function getNbreVue($id)
    {

        return $this->em->getRepository(NombreClick::class)->getNombreVue($id);
    }

    public function getPermissionIfDifferentNull($group, $route)
    {
        $repo = $this->em->getRepository(ModuleGroupePermition::class)->getPermission($group, $route);
        //dd($repo);
        if ($repo != null) {
            return $repo['code'];
        } else {
            return $repo;
        }
    }

    public function liste()
    {


        return  $repo = $this->em->getRepository(Groupe::class)->afficheGroupes();
    }

    public function listeParent()
    {

        return $this->em->getRepository(Groupe::class)->affiche();
    }
    //public function listeModule
    public function listeGroupe()
    {
        $array = [
            'module' => 'modules',
            'app_config_parametre_index' => 'Parametrage général',
            'app_utilisateur_groupe_index' => 'Gestion groupe utilisateur',
            'app_utilisateur_utilisateur_index' => 'Gestion des utilisateur',
            'app_demande_demande_index' => 'Gestion des demandes',
            'app_utilisateur_permition_index' => 'Gestion des rôles',
            'app_utilisateur_employe_index' => 'Gestion des employés',

        ];

        return $array;
    }
    //    public function verifyanddispatch() {
    //
    //
    //
    //    }
}
