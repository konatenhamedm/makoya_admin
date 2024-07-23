<?php

namespace  App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use App\Entity\Notification;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\NotificationPrestataire;
use App\Repository\NotificationPrestataireRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/notification/prestataire')]
class ApiNotificationPrestataireController extends ApiInterface
{


    #[Route('/{email}', methods: ['GET'])]
    /**
     * Retourne la liste des notificationPrestataires.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotificationPrestataire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'notificationPrestataires')]
    // #[Security(name: 'Bearer')]
    public function index($email, NotificationPrestataireRepository $notificationPrestataireRepository): Response
    {
        try {
            $notificationPrestataires = $notificationPrestataireRepository->findNotifications($email);
            $i = 0;
            $tabCivilite = [];


            foreach ($notificationPrestataires as $key => $value) {
                $tabCivilite[$i]['id'] = $value->getId();
                $tabCivilite[$i]['title'] = $value->getNotification()->getTitre();
                $tabCivilite[$i]['content'] = $value->getNotification()->getMessage();
                $tabCivilite[$i]['receivedAt'] = $value->getNotification()->getDateCreation()->format('Y-m-d H:i:s');;
                $tabCivilite[$i]['read'] = $value->getNotification()->isEtat();

                /*   id: 7,
                title: "Notification 7",
                content: "Contenu de la notification 7",
                receivedAt: new Date(),
                read: true,
 */
                $i++;
            }
            $this->setMessage("Operation reussie");
            $response = $this->response($tabCivilite);
        } catch (\Throwable $th) {
            //throw $th;
            $this->setMessage("erreur");
            $response = $this->response('[]');
        }

        return $response;
    }



    #[Route('/read',  methods: ['POST'])]
    /**
     * Permet de changer le statut d'une notification.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotificationPrestataire::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [

                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'notificationPrestataires')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, NotificationRepository $notificationRepository, NotificationPrestataireRepository $notificationPrestataireRepository): Response
    {
        $data = json_decode($request->getContent());
        $notificationPrestataire = $notificationPrestataireRepository->find($data->id);
        try {
            if ($notificationPrestataire) {
                if ($notificationPrestataire->getNotification()->isEtat() == false) {
                    $notificationPrestataire->getNotification()->setEtat(true);
                    $notificationRepository->save($notificationPrestataire->getNotification(), true);
                }

                $response = $this->response('true');
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($notificationPrestataire);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response('[]');
        }


        return $response;
    }


    //const TAB_ID = 'parametre-tabs';

    #[Route('/delete/{id}',  methods: ['DELETE'])]
    /**
     * permet de supprimer un(e) notificationPrestataire.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotificationPrestataire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'notificationPrestataires')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, NotificationPrestataire $notificationPrestataire, NotificationPrestataireRepository $notificationPrestataireRepository): Response
    {
        try {

            if ($notificationPrestataire != null) {

                $notificationPrestataireRepository->remove($notificationPrestataire, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($notificationPrestataire);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response('[]');
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response('[]');
        }
        return $response;
    }

    #[Route('/delete/all',  methods: ['DELETE'])]
    /**
     * Permet de supprimer plusieurs notificationPrestataires.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: NotificationPrestataire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'notificationPrestataires')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, NotificationPrestataireRepository $notificationPrestataireRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $notificationPrestataire = $notificationPrestataireRepository->find($value['id']);

                if ($notificationPrestataire != null) {
                    $notificationPrestataireRepository->remove($notificationPrestataire);
                }
            }
            $this->setMessage("Operation effectuées avec success");
            $response = $this->response('[]');
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response('[]');
        }
        return $response;
    }
}
