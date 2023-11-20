<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Repository\DepartementRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route(path: '/home', name: 'app_default')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/ads/error_page', name: 'page_error_index', methods: ['GET', 'POST'])]
    public function errorIndex(Request $request): Response
    {
        return $this->render('error.html.twig', []);
    }

    #[Route(path: '/api/departement', name: 'app_departement_api')]
    public function api(Request $request, RegionRepository $regionRepository, DepartementRepository $departementRepository)
    {
        $departements = [
            [
                'id' => "Abidjan 1",
                'data' => [
                    [
                        'departement' => "ABIDJAN",
                    ]
                ]
            ],
            [
                'id' => "LACS",
                'data' => [
                    [
                        'departement' => "ATTIEGOUAKRO",
                    ],
                    [
                        'departement' => "YAMOUSSOUKRO",
                    ]
                ]
            ],
            [
                'id' => "NAWA",
                'data' => [
                    [
                        'departement' => "SOUBRE",
                    ],
                    [
                        'departement' => "GUEYO",
                    ]
                ]
            ],
            [
                'id' => "SAN PEDRO",
                'data' => [
                    [
                        'departement' => "SAN PEDRO",
                    ],
                    [
                        'departement' => "TABOU",
                    ]
                ]
            ],
            [
                'id' => "GBÔKLE",
                'data' => [
                    [
                        'departement' => "SASSANDRA",
                    ],
                    [
                        'departement' => "FRESCO",
                    ]
                ]
            ],
            [
                'id' => "Indénié Djuablin",
                'data' => [
                    [
                        'departement' => "ABENGOUROU",
                    ],
                    [
                        'departement' => "AGNIBILEKRO",
                    ],
                    [
                        'departement' => "BETTIE",
                    ]
                ]
            ],
            [
                'id' => "SUD COMOE",
                'data' => [
                    [
                        'departement' => "ABOISSO",
                    ],
                    [
                        'departement' => "ADIAKE",
                    ],
                    [
                        'departement' => "GRAND BASSAM",
                    ],
                    [
                        'departement' => "TIAPOUM",
                    ]

                ]
            ],
            [
                'id' => "FOLON",
                'data' => [
                    [
                        'departement' => "MINIGNAN",
                    ],
                    [
                        'departement' => "KANIASSO",
                    ]
                ]
            ],
            [
                'id' => "Kabadougou",
                'data' => [
                    [
                        'departement' => "ODIENNE",
                    ],
                    [
                        'departement' => "MADINANI",
                    ],
                    [
                        'departement' => "KANSAMATIGUILAIASSO",
                    ]
                ]
            ],
            [
                'id' => "GÔH",
                'data' => [
                    [
                        'departement' => "GAGNOA",
                    ],
                    [
                        'departement' => "OUME",
                    ]
                ]
            ],
            [
                'id' => "LÔH-DJIBOUA",
                'data' => [
                    [
                        'departement' => "DIVO",
                    ],
                    [
                        'departement' => "LAKOTA",
                    ],
                    [
                        'departement' => "GUITRY",
                    ]
                ]
            ],
            [
                'id' => "BELIER",
                'data' => [
                    [
                        'departement' => "DIDIEVI",
                    ],
                    [
                        'departement' => "TIEBISSOU",
                    ],
                    [
                        'departement' => "TOUMODI",
                    ]
                ]
            ],
            [
                'id' => "IFFOU",
                'data' => [
                    [
                        'departement' => "DAOUKRO",
                    ],
                    [
                        'departement' => "M’BAHIAKRO",
                    ],
                    [
                        'departement' => "PRIKRO",
                    ]
                ]
            ],
            [
                'id' => "N’ZI",
                'data' => [
                    [
                        'departement' => "DIMBOKRO",
                    ],
                    [
                        'departement' => "BOCANDA",
                    ]
                ]
            ],
            [
                'id' => "MORONOU",
                'data' => [
                    [
                        'departement' => "BONGOUANOU",
                    ],
                    [
                        'departement' => "ARRAH",
                    ],
                    [
                        'departement' => "M’BATTO",
                    ]
                ]
            ],
            [
                'id' => "GRANDS PONTS",
                'data' => [
                    [
                        'departement' => "DABOU",
                    ],
                    [
                        'departement' => "JACQUEVILLE",
                    ],
                    [
                        'departement' => "GRAND LAHOU",
                    ]

                ]
            ],
            [
                'id' => "Agneby Tiassa",
                'data' => [
                    [
                        'departement' => "AGBOVILLE",
                    ],
                    [
                        'departement' => "TIASSALE",
                    ],
                    [
                        'departement' => "SIKENSI",
                    ]
                ]
            ],
            [
                'id' => "LA ME",
                'data' => [
                    [
                        'departement' => "ADZOPE",
                    ],
                    [
                        'departement' => "ALEPE",
                    ],
                    [
                        'departement' => "AKOUPE",
                    ],
                    [
                        'departement' => "YAKASSE ATTOBROU",
                    ]
                ]
            ],
            [
                'id' => "TONKPI",
                'data' => [
                    [
                        'departement' => "MAN",
                    ],
                    [
                        'departement' => "ZOUAN-HOUNIEN",
                    ],
                    [
                        'departement' => "BIANKOUMA",
                    ],
                    [
                        'departement' => "DANANE",
                    ]
                ]
            ],
            [
                'id' => "Cavally",
                'data' => [
                    [
                        'departement' => "GUIGLO",
                    ],
                    [
                        'departement' => "BLOLEQUIN",
                    ],
                    [
                        'departement' => "TOULEPLEU",
                    ]
                ]
            ],
            [
                'id' => "GUEMON",
                'data' => [
                    [
                        'departement' => "DUEKOUE",
                    ],
                    [
                        'departement' => "BANGOLO",
                    ],
                    [
                        'departement' => "KOUIBLY",
                    ]
                ]
            ],
            [
                'id' => "HAUT SASSANDRA",
                'data' => [
                    [
                        'departement' => "DALOA",
                    ],
                    [
                        'departement' => "ISSIA",
                    ],
                    [
                        'departement' => "VAVOUA",
                    ],
                    [
                        'departement' => "ZOUKOUGBEU",
                    ]
                ]
            ],
            [
                'id' => "MARAHOUE",
                'data' => [
                    [
                        'departement' => "BOUAFLE",
                    ],
                    [
                        'departement' => "SINFRA",
                    ],
                    [
                        'departement' => "ZUENOULA",
                    ]
                ]
            ],
            [
                'id' => "PORO",
                'data' => [
                    [
                        'departement' => "KORHOGO",
                    ],
                    [
                        'departement' => "SINEMATIALI",
                    ],
                    [
                        'departement' => "DIKODOUGOU",
                    ]
                ]
            ],
            [
                'id' => "TCHOLOGO",
                'data' => [
                    [
                        'departement' => "FERKESSEDOUGOU",
                    ],
                    [
                        'departement' => "OUANGOLODOUGOU",
                    ],
                    [
                        'departement' => "KONG",
                    ]
                ]
            ],
            [
                'id' => "BAGOUE",
                'data' => [
                    [
                        'departement' => "BOUNDIALI",
                    ],
                    [
                        'departement' => "TENGRELA",
                    ],
                    [
                        'departement' => "KOUTO",
                    ]
                ]
            ],
            [
                'id' => "HAMBOL",
                'data' => [
                    [
                        'departement' => "KATIOLA",
                    ],
                    [
                        'departement' => "DABAKALA",
                    ],
                    [
                        'departement' => "NIAKARAMADOUGOU",
                    ]
                ]
            ],
            [
                'id' => "GBEKE",
                'data' => [
                    [
                        'departement' => "BOUAKE",
                    ],
                    [
                        'departement' => "BOTRO",
                    ],
                    [
                        'departement' => "BEOUMI",
                    ],
                    [
                        'departement' => "SAKASSOU",
                    ]
                ]
            ],
            [
                'id' => "BERE",
                'data' => [
                    [
                        'departement' => "MANKONO",
                    ],
                    [
                        'departement' => "KOUNAHIRI",
                    ]
                ]
            ],
            [
                'id' => "BAFING",
                'data' => [
                    [
                        'departement' => "TOUBA",
                    ],
                    [
                        'departement' => "KORO",
                    ],
                    [
                        'departement' => "OUANINOU",
                    ]
                ]
            ],
            [
                'id' => "WORODOUGOU",
                'data' => [
                    [
                        'departement' => "SEGUELA",
                    ],
                    [
                        'departement' => "KANI",
                    ]
                ]
            ],
            [
                'id' => "BOUNKANI",
                'data' => [
                    [
                        'departement' => "BOUNA",
                    ],
                    [
                        'departement' => "DOROPO",
                    ],
                    [
                        'departement' => "NASSIAN",
                    ],
                    [
                        'departement' => "TEHINI",
                    ]
                ]
            ],
            [
                'id' => "GONTOUGO",
                'data' => [
                    [
                        'departement' => "BONDOUKOU",
                    ],
                    [
                        'departement' => "SANDEGUE",
                    ],
                    [
                        'departement' => "KOUN-FAO",
                    ],
                    [
                        'departement' => "TRANSUA",
                    ],
                    [
                        'departement' => "TANDA",
                    ]
                ]
            ],
        ];

        foreach ($departements as $departement) {

            $region = $regionRepository->findOneBy(['nom' => $departement['id']]);

            foreach ($departement['data'] as $key => $value) {
                //$departement['data'][$key]['region'] = $region; 
                //dd($value['departement']);
                $departement = new Departement();
                $departement->setNom($value['departement']);
                $departement->setCode($this->numero());
                $departement->setRegion($region);
                $departementRepository->save($departement, true);
            }
        }


        return $this->json([
            'departement' => $departements
        ], 200);
    }

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Departement::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('DEP' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }
}
