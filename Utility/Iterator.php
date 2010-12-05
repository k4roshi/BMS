<?php 
/** 
 * <p>Title: Double linked list</p> 
 * <p>Description: Implementation of a double linked list in PHP</p> 
 * @author Oddleif Halvorsen | leif-h@online.no 
 * @version 1.2 
 */ 
  
class Iterator{ 
    var $currentNode; 
    var $previousNode; 
    var $nextNode; 
    var $list; //should not be nessesary to use. Ineffecive on large lists. 

    /** 
     * Constructs an iterator. 
     * @param &$head The object/value of the node 
     * @param &$list The LinkedList, used for easy deletes of nodes. 
     */ 
    function Iterator(&$head, &$list){ 
        $this->currentNode = &$head; 
        $this->previousNode = &$this->currentNode->getPrevious(); 
        $this->nextNode = &$this->currentNode->getNext(); 

        $this->list = &$list; 
    } 
     
    /** 
     * Checks if more nodes exist 
     * @return TRUE == hasNext || FALSE == no next node. 
     */ 
    function hasNext(){ 
        //current node must exist and be different from NULL OR next node exists. 
        return (($this->currentNode != NULL && $this->currentNode->getNext() != NULL) || $this->nextNode != NULL ? TRUE : FALSE); 
    } 

    /** 
     * Get the the next node. 
     * Checks if there is a next node, if no next node it 
     * returns false. 
     * @return The object at current node 
     */ 
    function getNext(){ 
        if($this->hasNext()){ 
            //checks if current node is deleted. 
            if($this->currentNode != NULL) 
                $this->currentNode = &$this->currentNode->getNext(); 
            else 
                $this->currentNode = &$this->nextNode; 

            $this->previousNode = &$this->currentNode->getPrevious(); 
            $this->nextNode = &$this->currentNode->getNext(); 
             
            return $this->currentNode->getNodeValue(); 
        } 
         
        return FALSE; 
    } 
     
    /** 
     * Removes the current node 
     * Uses the list removeObjectAtIndex it it is the head 
     * or tail that is to be removed. 
     */ 
    function remove(){     
        if($this->currentNode->getPrevious() == NULL) 
            $this->list->removeObjectAtIndex(0); 
        else 
        if($this->currentNode->getNext() == NULL) 
            $this->list->removeObjectAtIndex(($this->list->size()-1)); 
        else{ 
                //updates the references for the before and after the current node. 
                $this->previousNode->setNext(&$this->nextNode); 
                $this->nextNode->setPrevious(&$this->previousNode); 
                $this->list->decSize(); 
        } 
        $this->currentNode->setNext(NULL); 
        $this->currentNode->setPrevious(NULL); 
        $this->currentNode = NULL; 
    } 

} 
?>