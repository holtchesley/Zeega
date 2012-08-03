<?php

namespace Zeega\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    public function findProjectsBySite($id)
    {
        $query= $this->getEntityManager()
				     ->createQueryBuilder()
				     ->add('select', 'p')
		             ->add('from', ' ZeegaDataBundle:Project p')
			         ->innerJoin('p.site', 'g')
			         ->add('where', 'g.id = :id')
			         ->andwhere('p.enabled = true')
			         ->setParameter('id',$id)
			         ->orderBy('p.id','DESC')
			         ->getQuery();

		return $query->getArrayResult();
    }
     
    public function findProjectsBySiteAndUser($siteId,$userId)
    {
     	$query= $this->getEntityManager()
				     ->createQueryBuilder()
				     ->add('select', 'p')
			   	     ->add('from', 'ZeegaDataBundle:Project p')
			         ->innerJoin('p.site', 's')
			         ->join('p.users', 'u')
			         ->add('where', 'u.id = :userId')
			         ->andWhere('s.id = :siteId')
			         ->andwhere('p.enabled = true')
			         ->setParameters(array('siteId'=>$siteId,'userId'=>$userId))
				     ->orderBy('p.id','DESC')
				     ->getQuery();
				    
		return $query->getArrayResult();
     }
     
     public function findProjectsByUser($userId,$limit,$published = null)
     {
          $qb = $this->getEntityManager()->createQueryBuilder();
      	  $qb->add('select', 'p')
 			 ->add('from', 'ZeegaDataBundle:Project p')
             ->join('p.users', 'u')
             ->add('where', 'u.id = :userId')
             ->setParameters(array('userId'=>$userId))
             ->andwhere('p.enabled = true')
             ->orderBy('p.id','DESC')
             ->setMaxResults($limit);
 				       
 		if(isset($published))
 		{
 	        $qb->andwhere('p.published = :published')->setParameters(array('published' => $published));
 		}

 		return $qb->getQuery()->getResult();
      }
}
