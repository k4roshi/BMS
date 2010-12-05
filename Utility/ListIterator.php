<?php 
/** 
 * <p>Title: Double linked list</p> 
 * <p>Description: Implementation of a double linked list in PHP</p> 
 * @author Oddleif Halvorsen | leif-h@online.no 
 * @version 1.2 
 */ 

include_once("Iterator.php");  

class ListIterator extends Iterator{ 

    /** 
     * Constructs a ListNode. 
     * @param &$head The start of the list 
     * @param &$list The LinkedList, nessesary for updating the size of the list on removal of nodes. 
     */ 
    function ListIterator(&$head, &$list){ 
        parent::Iterator(&$head, &$list); 
    } 
     
    /** 
     * Checks if more nodes exist 
     * @return TRUE == hasNext || FALSE == no next node. 
     */ 
    function hasPrevious(){ 
        //it has a previous value if $this->previous != NULL or current node is set an has a previous value set. 
        return (($this->currentNode != NULL && $this->currentNode->getPrevious() != NULL) || $this->previousNode != NULL ? TRUE : FALSE); 
    } 
     
    /** 
     * Get the reference to the previous node. 
     * @param &$node The next node. || FALSE if no next node exists. 
     */ 
    function getPrevious(){ 
        if($this->hasPrevious()){ 
            $this->currentNode = &$this->currentNode->getPrevious(); //current is the node between previous and next 
            $this->previousNode = &$this->currentNode->getPrevious(); //previous is the node closer to head 
            $this->nextNode = &$this->currentNode->getNext(); //next is the node closer to tail 

            return $this->currentNode->getNodeValue(); 
        } 
         
        return FALSE; 
    } 
} 
?>