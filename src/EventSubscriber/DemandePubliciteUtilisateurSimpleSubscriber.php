<?php

namespace App\EventSubscriber;

use App\Entity\HistoriqueDemande;
use App\Entity\LigneDemande;
use App\Entity\LigneMouvement;
use App\Entity\MouvementDemande;
use App\Entity\Notification;
use App\Entity\NotificationPrestataire;
use App\Entity\NotificationUtilisateurSimple;
use App\Entity\PrestataireService;
use App\Entity\PublicitePrestataire;
use App\Entity\WorkflowServicePrestataire;
use App\Repository\ArticleMagasinRepository;
use App\Repository\MagasinRepository;
use App\Repository\SensRepository;
use App\Repository\UserFrontRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;
use function Symfony\Component\Config\Definition\Builder\find;

class DemandePubliciteUtilisateurSimpleSubscriber implements EventSubscriberInterface
{
  private $em;
  private $sensRepository;
  protected $security;
  protected $magasin;
  protected $repo;
  protected  $service;
  protected  $workflow;

  private function numero()
  {

    $query = $this->em->createQueryBuilder();
    $query->select("count(a.id)")
      ->from(PublicitePrestataire::class, 'a');

    $nb = $query->getQuery()->getSingleScalarResult();
    if ($nb == 0) {
      $nb = 1;
    } else {
      $nb = $nb + 1;
    }
    return (date("y") . 'PUP' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
  }

  public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow, UserFrontRepository $repo)
  {
    ///  $this->security = $security;
    $this->em = $em;
    $this->workflow = $workflow;
    $this->repo = $repo;
  }

  public function handleValidation(TransitionEvent $event): void
  {

    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();

    //  dd($entity);
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();


    $publicite = new PublicitePrestataire();
    $publicite->setCode($this->numero());
    $publicite->setLibelle($entity->getLibelle());
    $publicite->setType("Utilisateur simple");
    $publicite->setDateCreation(new \DateTime());
    $publicite->setDateFin($entity->getDateFin());
    $publicite->setDateDebut($entity->getDateDebut());
    $publicite->setUtilisateur($this->repo->findOneBy(array('reference' => $entity->getUtilisateur()->getReference())));
    $this->em->persist($publicite);
    $this->em->flush();

    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message validation')
      ->setMessage("Nous venons par ce message vous annoncer que votre demande de publicté  à été validée avec success nous vous contacterons pour plus de details");


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationUtilisateurSimple();
    $notifcationPrestataire->setUtilisateurSimple($entity->getUtilisateur());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush();
  }

  public function handleRejeter(TransitionEvent $event)
  {
    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();
    $entity->setDateValidation(new \DateTime());
    $this->em->flush();

    $notification = new Notification();
    $notification->setDateCreation(new DateTime())
      ->setEtat(false)
      ->setTitre('Message')
      ->setMessage("Nous venons par ce message vous annoncer que votre demande de publicité à été réjetée");


    $this->em->persist($notification);
    $this->em->flush();

    $notifcationPrestataire = new NotificationUtilisateurSimple();
    $notifcationPrestataire->setUtilisateurSimple($entity->getUtilisateur());
    $notifcationPrestataire->setNotification($notification);
    $this->em->persist($notifcationPrestataire);
    $this->em->flush();
  }



  public static function getSubscribedEvents(): array
  {
    return [
      'workflow.add_demande_publicite_utilisateur_simple.transition.passer' => 'handleValidation',
      'workflow.add_demande_publicite_utilisateur_simple.transition.rejeter' => 'handleRejeter',

    ];
  }
}
