<?php

namespace App\Controller;

use App\Dto\SwarmDto;
use App\Entity\Hive;
use App\Entity\Swarm;
use App\Form\SwarmType;
use App\Entity\Apiculteur;
use App\Form\SwarmTransertType;
use App\Repository\SwarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/swarm', name: 'app_swarm_')]
final class SwarmController extends AbstractController
{
    #[Route('/hive/{id}', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Hive $hive,
        #[CurrentUser] Apiculteur $user,
        SwarmRepository $repo,
        EntityManagerInterface $em,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        if (null === $hive->getSwarm()) {
            $swarms = $repo->findByBeekeeper($user);
            $dtos = [];
            foreach ($swarms as $swarmItem) {
                /**@var Swarm $swarmItem */
                $swarmName = $swarmItem->getName();
                $hiveName = 'Non affecté';
                if (null !== $swarmItem->getHive()) {
                    $hiveName = $swarmItem->getHive()->getName();
                }
                $name = "{$swarmName} - {$hiveName}";
                $dtos[] = new SwarmDto($swarmItem->getId(), $name);
            }
            $form = $this->createForm(SwarmTransertType::class, null, ['dtos' => $dtos]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newId = $form->get('swarmList')->getNormData();
                $newSwarm = $repo->findOneBy(['id' => $newId]);
                $newSwarm->setHive($hive);
                $em->flush();
                return $this->redirectToRoute('app_swarm_index', ['id' => $hive->getId()]);
            }
            return $this->render("swarm/index_empty.html.twig", [
                'hive' => $hive,
                'form' => $form
            ]);
        }
        return $this->render("swarm/index.html.twig", [
            'hive' => $hive,
        ]);
    }

    #[Route('/add/{id}', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        Hive $hive,
        #[CurrentUser] Apiculteur $user,
    ): Response {
        $this->denyAccessUnlessGranted('own', $hive);
        $swarm = new Swarm();
        $form = $this->createForm(SwarmType::class, $swarm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $hive->getSwarm()) {
                $hive->setSwarm(null);
            }
            $swarm->setBeekeeper($user);
            $swarm->setHive($hive);
            $em->persist($swarm);
            $em->flush();
            return $this->redirectToRoute('app_swarm_index', ['id' => $hive->getId()]);
        }
        return $this->render("swarm/add.html.twig", [
            'hive' => $hive,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        Swarm $swarm,
    ): Response {
        $this->denyAccessUnlessGranted('own', $swarm);
        $id = $swarm->getHive()->getId();
        if ($this->isCsrfTokenValid('delete' . $swarm->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($swarm);
            $em->flush();
        }
        return $this->redirectToRoute('app_swarm_index', ['id' => $id]);
    }

    #[Route('/{id}/update', name: 'edit', methods: ['GET', 'POST'])]
    public function update(
        Swarm $swarm,
        EntityManagerInterface $em,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('own', $swarm);
        $form = $this->createForm(SwarmType::class, $swarm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_swarm_index', ['id' => $swarm->getHive()->getId()]);
        }
        return $this->render("swarm/update.html.twig", [
            'hive' => $swarm->getHive(),
            'form' => $form
        ]);
    }

    #[Route('/{id}/translate', name: 'translate', methods: ['GET', 'POST'])]
    public function translate(
        #[CurrentUser] Apiculteur $user,
        Swarm $swarm,
        EntityManagerInterface $em,
        SwarmRepository $repo,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('own', $swarm);
        $oldHive = $swarm->getHive();
        $swarms = $repo->findByBeekeeper($user);
        $dtos = [];
        foreach ($swarms as $swarmItem) {
            /**@var Swarm $swarmItem */
            if ($swarm !== $swarmItem) {
                $swarmName = $swarmItem->getName();
                $hiveName = 'Non affecté';
                if (null !== $swarmItem->getHive()) {
                    $hiveName = $swarmItem->getHive()->getName();
                }
                $name = "{$swarmName} - {$hiveName}";
                $dtos[] = new SwarmDto($swarmItem->getId(), $name);
            }
        }
        $form = $this->createForm(SwarmTransertType::class, null, ['dtos' => $dtos]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newId = $form->get('swarmList')->getNormData();
            $newSwarm = $repo->findOneBy(['id' => $newId]);
            $swarm->setHive(null);
            $newSwarm->setHive($oldHive);
            $em->flush();
            return $this->redirectToRoute('app_swarm_index', ['id' => $oldHive->getId()]);
        }
        return $this->render('swarm/translate.html.twig', [
            'form' => $form,
            'swarm' => $swarm,
            'hiveId' => $oldHive->getId()
        ]);
    }
}
