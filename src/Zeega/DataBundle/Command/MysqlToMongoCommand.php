<?php

/*
* This file is part of Zeega.
*
* (c) Zeega <info@zeega.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Zeega\DataBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Zeega\CoreBundle\Helpers\ResponseHelper;
use Zeega\DataBundle\Entity\Item;
use Zeega\DataBundle\Document\Item as MongoItem;
use Zeega\DataBundle\Entity\User;
use Zeega\DataBundle\Document\User as MongoUser;
use Zeega\DataBundle\Document\Project as MongoProject;
use Zeega\DataBundle\Document\Sequence as MongoSequence;
use Zeega\DataBundle\Document\Frame as MongoFrame;

/**
 * Updates a task status
 *
 */
class MysqlToMongoCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */       
    protected function configure()
    {
        $this->setName('zeega:data:mongodb:import-items')
             ->setDescription('Moves items from mysql to mongo')
             ->addOption('items', null, InputOption::VALUE_NONE , 'Items')
             ->addOption('users', null, InputOption::VALUE_NONE , 'Items')
             ->addOption('projects', null, InputOption::VALUE_NONE , 'Items')
             ->addOption('create_project', null, InputOption::VALUE_NONE , 'Creates a project')
             ->setHelp("Help");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $items = $input->getOption('items');
        $users = $input->getOption('users');
        $projects = $input->getOption('projects');
        $createProject = $input->getOption('create_project');
        
        if( $items ) {
            self::importItems($output);
        } else if ( $users ) {
            self::importUsers($output);
        } else if ( $createProject ) {
            self::createProject($output);
        } else if ( $projects ) {
            self::importProjects($output);
        }
    }

    private function createProject(OutputInterface $output) {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $mongoUser = new MongoUser();
        $dm->persist($mongoUser);
        $dm->flush();


        $mongoSequence = new MongoSequence();
        $mongoSequence->setTitle('sequence yo');

        $mongoProject = new MongoProject();
        $mongoProject->addUsers($mongoUser);
        $mongoProject->addSequences($mongoSequence);
        $dm->persist($mongoProject);
        $dm->flush();

        $mItem = $dm->getRepository('ZeegaDataBundle:Project')->findOneBy(array("users"=>$mongoUser->getId()));

        echo $mItem->getId();
    }

    private function importUsers(OutputInterface $output)
    {
        $output->writeln('<info>Importing users</info>');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $mysqlUsers = $em->getRepository('ZeegaDataBundle:User')->findAll();

        foreach($mysqlUsers as $user) {            
            $dm->persist($user);
        }
        $dm->flush();
    }

    private function importItems(OutputInterface $output)
    {
        $output->writeln('<info>Importing items</info>');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $mysqlItems = $em->getRepository('ZeegaDataBundle:Item')->findAll();

        foreach($mysqlItems as $item) {            
            $mItem = $dm->getRepository('ZeegaDataBundle:Item')->findOneBy(array("uri"=>$item->getUri()));
            if(!isset($mItem)) {
                $mongoItem = new MongoItem();
                $mongoItem->setTitle($item->getTitle());
                $mongoItem->setDescription($item->getDescription());
                $mongoItem->setText($item->getText());
                $mongoItem->setUri($item->getUri());
                $mongoItem->setAttributionUri($item->getAttributionUri());
                $mongoItem->setDateCreated($item->getDateCreated());
                $mongoItem->setMediaType($item->getMediaType());
                $mongoItem->setLayerType($item->getLayerType());
                $mongoItem->setThumbnailUrl($item->getThumbnailUrl());
                $mongoItem->setMediaGeoLatitude($item->getMediaGeoLatitude());
                $mongoItem->setMediaDateCreated($item->getMediaDateCreated());
                $mongoItem->setMediaCreatorUsername($item->getMediaCreatorUsername());
                $mongoItem->setArchive($item->getArchive());
                $mongoItem->setLocation($item->getLocation());
                $mongoItem->setLicense($item->getLicense());
                $mongoItem->setAttributes($item->getAttributes());
                $mongoItem->setEnabled($item->getEnabled());
                $mongoItem->setPublished($item->getPublished());
                $mongoItem->setTags($item->getTags());
                $mongoItem->setDateUpdated($item->getDateUpdated());
                $mongoItem->setIngestedBy($item->getIngestedBy());
                $mongoItem->setDuration($item->getDuration());

                $dm->persist($mongoItem);
                $dm->flush();

                $childItems = $item->getChildItems();
                if( isset($childItems) ) {
                    foreach($childItems as $childItem) {

                        $mChildItem = $dm->getRepository('ZeegaDataBundle:Item')->findOneBy(array("uri"=>$childItem->getUri()));

                        if(!isset($mChildItem)) {
                            $mChildItem = new MongoItem();
                            $mChildItem->setTitle($childItem->getTitle());
                            $mChildItem->setDescription($childItem->getDescription());
                            $mChildItem->setText($childItem->getText());
                            $mChildItem->setUri($childItem->getUri());
                            $mChildItem->setAttributionUri($childItem->getAttributionUri());
                            $mChildItem->setDateCreated($childItem->getDateCreated());
                            $mChildItem->setMediaType($childItem->getMediaType());
                            $mChildItem->setLayerType($childItem->getLayerType());
                            $mChildItem->setThumbnailUrl($childItem->getThumbnailUrl());
                            $mChildItem->setMediaGeoLatitude($childItem->getMediaGeoLatitude());
                            $mChildItem->setMediaDateCreated($childItem->getMediaDateCreated());
                            $mChildItem->setMediaCreatorUsername($childItem->getMediaCreatorUsername());
                            $mChildItem->setArchive($childItem->getArchive());
                            $mChildItem->setLocation($childItem->getLocation());
                            $mChildItem->setLicense($childItem->getLicense());
                            $mChildItem->setAttributes($childItem->getAttributes());
                            $mChildItem->setEnabled($childItem->getEnabled());
                            $mChildItem->setPublished($childItem->getPublished());
                            $mChildItem->setTags($childItem->getTags());
                            $mChildItem->setDateUpdated($childItem->getDateUpdated());
                            $mChildItem->setIngestedBy($childItem->getIngestedBy());
                            $mChildItem->setDuration($childItem->getDuration());
                        }

                        $mongoItem->addChildItems($mChildItem);
                    }    
                }
                $dm->persist($mongoItem);
                $dm->flush();
            }
        }
        $dm->flush();        
    }

    private function importProjects(OutputInterface $output)
    {
        $output->writeln('<info>Importing projects</info>');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $mysqlProjects = $em->getRepository('ZeegaDataBundle:Project')->findAll();

        foreach($mysqlProjects as $mysqlProject) {
            $project = new MongoProject();
            $project->setTitle($mysqlProject->getTitle());
            $project->setPublished($mysqlProject->getPublished());
            $project->setMobile($mysqlProject->getMobile());
            $project->setDateCreated($mysqlProject->getDateCreated());

            $project->setEnabled($mysqlProject->getEnabled());
            $project->setTags($mysqlProject->getTags());
            $project->setAuthors($mysqlProject->getAuthors());
            $project->setCoverImage($mysqlProject->getCoverImage());
            $project->setEstimatedTime($mysqlProject->getEstimatedTime());

            $project->setDateUpdated($mysqlProject->getDateUpdated());
            $project->setItemId($mysqlProject->getItemId());
            $project->setDescription($mysqlProject->getDescription());
            $project->setLocation($mysqlProject->getLocation());
            $project->setDatePublished($mysqlProject->getDatePublished());

            $mysqlSequences = $em->getRepository('ZeegaDataBundle:Sequence')->findBy(array("project" => $mysqlProject->getId()));
            
            foreach($mysqlSequences as $seq) {
                $seqId = $seq->getId();
                $id = new \MongoId();
                echo $id;
                $sequence = new MongoSequence();
                $sequence->setId($id);
                $sequence->setTitle($seq->getTitle());
                $sequence->setAttr($seq->getAttr());
                $sequence->setEnabled($seq->getEnabled());
                $sequence->setPersistentLayers($seq->getPersistentLayers());
                $sequence->setDescription($seq->getDescription());
                $sequence->setAdvanceTo($seq->getAdvanceTo());
                
                $mysqlFrames = $em->getRepository('ZeegaDataBundle:Frame')->findFramesBySequenceId($seqId);
                $frames = array();
                foreach($mysqlFrames as $fram) {
                    $frameId = new \MongoId();
                    $frame = new MongoFrame();
                    $frame->setId($frameId);
                    $frame->setAttr($fram->getAttr());
                    $frame->setThumbnailUrl($fram->getThumbnailUrl());
                    $frame->setEnabled($fram->getEnabled());
                    $frame->setControllable($fram->getControllable());
                    $project->addFrames($frame);
                    $frames[] = (string) $frameId;

                    $layersIds = $fram->getLayers();
                    $layers = array();
                    if (is_array($layersIds) ) {
                        foreach($layersIds as $layerId) {
                            $mySqlLayer = $em->getRepository('ZeegaDataBundle:Layer')->findOnById($layerId);

                            $layerId = new \MongoId();
                            $layer = new MongoLayer();
                            $layer->setType($mySqlLayer->getType());
                            $layer->setText($mySqlLayer->getText());
                            $layer->setAttr($mySqlLayer->getAttr());
                            $layer->setEnabled($mySqlLayer->getEnabled());

                            $layers[] = (string) $layerId;

                            $project->addLayers($layer);
                        }    
                    }
                    
                    $frame->setLayers($layers);
                    $project->addFrames($frame);
                }   
                $sequence->setFrames($frames);
                $project->addSequences($sequence);
            }

            //var_dump($project);
            //return;
            $dm->persist($project);
            $dm->flush();
            return;
        }
    }
}