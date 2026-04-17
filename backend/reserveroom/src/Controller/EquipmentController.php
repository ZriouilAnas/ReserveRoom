<?php
namespace App\Controller;

use App\Entity\Equipment;
use App\Enum\EquipmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api', name: 'api_')]
class EquipmentController extends AbstractController
{
    #[Route('/equipment', name: 'equipment_index', methods:['get'] )]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {

        $equipments = $entityManager
            ->getRepository(Equipment::class)
            ->findAll();
    
        $data = [];

        foreach ($equipments as $equipment) {
           $data[] = [
               'id' => $equipment->getId(),
               'name' => $equipment->getEquipmentName(),
               'type' => $equipment->getType(),
               'quantity' => $equipment->getQuantity(),
           ];
        }
    
        return $this->json($data);
    }


    #[Route('/equipment', name: 'equipment_create', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $equipment = new Equipment();
        $equipment->setEquipmentName($data['equipmentName']);
        $equipment->setType(EquipmentType::from($data['type']));
        $equipment->setQuantity($data['quantity']);

        $entityManager->persist($equipment);
        $entityManager->flush();
    
        $data =  [
            'id' => $equipment->getId(),
            'name' => $equipment->getEquipmentName(),
            'type' => $equipment->getType(),
            'quantity' => $equipment->getQuantity(),
        ];
            
        return $this->json($data);
    }


    #[Route('/equipment/{id}', name: 'equipment_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $equipment = $entityManager->getRepository(Equipment::class)->find($id);

        if (!$equipment) {

            return $this->json('No equipment found for id ' . $id, 404);
        }
    
        $data =  [
            'id' => $equipment->getId(),
            'name' => $equipment->getEquipmentName(),
            'type' => $equipment->getType(),
            'quantity' => $equipment->getQuantity(),
        ];
            
        return $this->json($data);
    }

    #[Route('/equipment/{id}', name: 'equipment_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $equipment = $entityManager->getRepository(Equipment::class)->find($id);

        if (!$equipment) {
            return $this->json('No equipment found for id ' . $id, 404);
        }

        $equipment->setEquipmentName($request->request->get('name'));
        $equipment->setType(EquipmentType::from($request->request->get('type')));
        $equipment->setQuantity($request->request->get('quantity'));
        $entityManager->flush();
    
        $data =  [
            'id' => $equipment->getId(),
            'name' => $equipment->getEquipmentName(),
            'type' => $equipment->getType(),
            'quantity' => $equipment->getQuantity(),
        ];
            
        return $this->json($data);
    }

    #[Route('/equipment/{id}', name: 'equipment_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $equipment = $entityManager->getRepository(Equipment::class)->find($id);

        if (!$equipment) {
            return $this->json('No equipment found for id ' . $id, 404);
        }

        $entityManager->remove($equipment);
        $entityManager->flush();

        return $this->json('Deleted an equipment successfully with id ' . $id);
    }
}