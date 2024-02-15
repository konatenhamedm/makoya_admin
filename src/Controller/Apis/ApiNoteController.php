<?php

namespace App\Controller\Apis;

use App\Controller\ApiInterface;
use App\Entity\Note;
use App\Entity\ServicePrestataire;
use App\Repository\NoteRepository;
use App\Repository\ServicePrestataireRepository;
use App\Repository\UserFrontRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\String\toString;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/note')]
class ApiNoteController extends ApiInterface
{
    #[Route('/', name: 'api_note', methods: ['GET'])]
    /**
     * Affiche toutes les notes.
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Note::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function getAll(NoteRepository $noteRepository): Response
    {
        try {

            $notes = $noteRepository->findAll();
            $response = $this->response($notes);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/getOne/{id}', name: 'api_note_get_one', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function getOne(?Note $note)
    {
        /*  $note = $noteRepository->find($id);*/
        try {
            if ($note) {
                $response = $this->response($note);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($note);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create', name: 'api_note_create', methods: ['POST'])]
    /**
     * Permet de créer une note.
     *
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function create(
        Request $request,
        NoteRepository $noteRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository
    ) {
        try {
            $data = json_decode($request->getContent());

            $note = $noteRepository->findOneBy(array('utilisateur' => $data->user, 'service' => $data->service));
            if ($note == null) {
                $note = new Note();
                $note->setUtilisateur($userFrontRepository->find($data->user));
                $note->setNote($data->note);
                $note->setService($servicePrestataireRepository->find($data->service));
                $noteRepository->save($note, true);

                // On retourne la confirmation
                $response = $this->response($note);
            } else {
                $note->setNote($data->note);
                $noteRepository->save($note, true);
                $response = $this->response($note);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{user}/{service}', name: 'api_note_update', methods: ['POST'])]
    /**
     * Permet de mettre à jour une note.
     *
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function update(
        Request $request,
        NoteRepository $noteRepository,
        ServicePrestataireRepository $servicePrestataireRepository,
        UserFrontRepository $userFrontRepository,
        $user,
        $service
    ) {
        try {
            $data = json_decode($request->getContent());

            $noteData = $noteRepository->findOneBy(array('utilisateur' => $user, 'service' => $service));
            if ($noteData == null) {
                $note = new Note();
                $note->setUtilisateur($userFrontRepository->find($user));
                $note->setNote($data->note);
                $note->setService($servicePrestataireRepository->find($service));
                $noteRepository->save($note, true);

                // On retourne la confirmation
                $response = $this->response($note);
            } else {
                $noteData->setNote($data->note);
                $noteRepository->save($noteData, true);
                $response = $this->response($noteData);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/delete/{id}', name: 'api_note_delete', methods: ['POST'])]
    /**
     * permet de supprimer une note en offrant un identifiant.
     *
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, NoteRepository $noteRepository, $id)
    {
        try {
            $data = json_decode($request->getContent());

            $note = $noteRepository->find($id);
            if ($note != null) {

                $noteRepository->remove($note, true);

                // On retourne la confirmation
                $response = $this->response($note);
            } else {
                $this->setMessage("cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }


    #[Route('/active/{id}', name: 'api_note_active', methods: ['GET'])]
    /**
     * Permet d'activer une note en offrant un identifiant.
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function active(?Note $note, NoteRepository $noteRepository)
    {
        /*  $note = $noteRepository->find($id);*/
        try {
            if ($note) {

                //$note->setCode("555"); //TO DO nous ajouter un champs active
                $noteRepository->save($note, true);
                $response = $this->response($note);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/active/multiple', name: 'api_note_active_multiple', methods: ['POST'])]
    /**
     * Permet de faire une desactivation multiple.
     *
     * @OA\Tag(name="Note")
     * @Security(name="Bearer")
     */
    public function multipleActive(Request $request, NoteRepository $noteRepository)
    {
        try {
            $data = json_decode($request->getContent());

            $listeNotes = $noteRepository->findAllByListId($data->ids);
            foreach ($listeNotes as $listeNote) {
                //$listeNote->setCode("555");  //TO DO nous ajouter un champs active
                $noteRepository->add($listeNote, true);
            }

            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
