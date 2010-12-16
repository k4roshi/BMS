<?php 

/** 
 * <p>Title: Double linked list</p> 
 * <p>Description: Implementation of a double linked list in PHP</p> 
 * @requires ListNode.php, Iterator.php and ListIterator.php 
 * @testscript LinkedListTest.php 
 * @author Oddleif Halvorsen | leif-h@online.no 
 * @version 1.2 
 */ 

include_once("ListNode.php"); 
     
class LinkedList{ 
     var $head;    // reference to the first node 
  var $tail;    // reference to the last node 
  var $size;    // The total number of nodes 

  function LinkedList(){ 
        //head and tail only points to the head and tail of the list, 
        //the list nodes does not point on head and tail! Hence, head and  
        //tail are not part of the list. 
        $this->head = new ListNode(); 
        $this->tail = new ListNode(); 
        $this->size = 0; 
  } 
     
    /** 
     * Adds a new object at a specific index, pushes 
     * the current further back into the list. 
     * @param $index Numeric value between 0 and $this->size()-1 
     * @param &$object A reference to the object that is to be inserted 
     * @return TRUE == insertino ok || FALSE == index out of bounds 
     */ 
    function addAtPosition($index, &$object){ 
        //checks if index is not out of bounds         
        if($index >= 0 && $index <= $this->size()-1){ 
            $newNode = $this->getNewNode($object); 
            $nodeAtIndex = &$this->getNode($index); 
             
            //new node at head 
            if($index == 0){ 
                $newNode->setNext(&$nodeAtIndex); 
                $nodeAtIndex->setPrevious(&$newNode); 
                $this->head->setNext(&$newNode); 
            } 
            else //somewhere else in the list 
                $this->insertNodeInList(&$newNode, &$nodeAtIndex); 

            $this->size++; 
            return TRUE; 
        } 
     
        return FALSE; 
  } 
     
    /** 
     * Inserts a new node into a the list on a position 
     * where it has nodes bothe before an after. 
     * NOT at index 0 or $this->size()-1 
     * @param &$newNode A reference to the node that is inserted in the list. 
     * @param &$nodeAtIndex The node it is pushing away. 
     */ 
    function insertNodeInList(&$newNode, &$nodeAtIndex){ 
        $previousNode = &$nodeAtIndex->getPrevious(); 
         
        $newNode->setPrevious(&$previousNode); 
        $newNode->setNext(&$nodeAtIndex); 
        $nodeAtIndex->setPrevious(&$newNode); 
        $previousNode->setNext(&$newNode); 
    } 

    /** 
     * Create a new node,  
     * Appends a new node to the tail 
     * @param &$element The element you want to append to the list. 
     */ 
  function add(&$element){ 
        $newNode = $this->getNewNode(&$element); 

        //A non empty list, mostly the case. 
        if(!$this->isEmpty()){ 
            $previousNode = &$this->tail->getPrevious(); 
            $previousNode->setNext(&$newNode); 
            $newNode->setPrevious(&$previousNode); 
        } 
        else //empty 
            $this->head->setNext(&$newNode); 
         
        $this->tail->setPrevious(&$newNode); 
         
        $this->size++; 
  } 
     
    /** 
     * Generates a new ListNode object. 
     * @param &$object The value of the ListNode 
     * @return A ListNode object 
     */ 
    function getNewNode(&$object){ 
        return new ListNode(&$object); 
    } 

    /** 
     * Empties the list. 
     */ 
  function clear(){ 
        $this->head = NULL; 
        $this->tail = NULL; 
        $this->size = 0; 
  } 

    /** 
     * Retrevies the object at the specified index. 
     * @param $index A value between 0 and $this->size() 
     * @return The object || FALSE == index out of bounds 
     */ 
    function get($index){ 
        //if the list is noe empty, and the index is a legal number 
        if(!$this->isEmpty() && (($index >= 0) && ($index < $this->size()))){ 
            if($index < ($this->size()/2)) //searches from head 
                $tmpNode = &$this->getNode($index); 
            else                                              //searches from tail 
                $tmpNode = &$this->getNodeReversed($index); 
                 
            return $tmpNode->getNodeValue(); 
        } 

        //index out of bounds. 
        return FALSE; 
    } 
         
    /** 
     * Checks if the list is empty 
     * @return TRUE == empty || FALSE == not empty 
     */ 
    function isEmpty(){ 
    return ( $this->size == 0 ? TRUE : FALSE ); 
    } 
     
    function removeObjectAtIndex($index){ 
        if(!$this->isEmpty() && ($index>=0 && $index<=($this->size()-1))){ 
            $nodeToRemove = &$this->getNode($index); 
            switch($index){ 
                case 0:    //removing head 
                                $nextNode = &$nodeToRemove->getNext(); 
                                $nextNode->setPrevious(NULL); 
                                $this->head->setNext(&$nextNode); 
                                break; 
                case ($this->size()-1): 
                                //removing tail 
                                $previousNode = &$nodeToRemove->getPrevious(); 
                                $previousNode->setNext(NULL); 
                                $this->tail->setPrevious(&$previousNode); 
                                break; 
                default: 
                                //gets the node before and after the deleted node 
                                $previousNode = &$nodeToRemove->getPrevious(); 
                                $nextNode = &$nodeToRemove->getNext(); 
                                //updates the references for the before and after node. 
                                $previousNode->setNext(&$nextNode); 
                                $nextNode->setPrevious(&$previousNode); 
                                break; 
            } 
            //compleatly removes the node 
            $nodeToRemove->setPrevious(NULL); 
            $nodeToRemove->setNext(NULL); 

            //decreases the size of the list. 
            $this->size--; 
            return TRUE; 
        } 
        return FALSE; 
    } 
     
    /** 
     * Retrevies a node at a spesific index 
     * @param $index A value between 0 and $this->size() 
     * @return The node || FALSE == index out of bounds 
     */ 
    function &getNode($index){ 
        //the list is not empty, and the index is not out of bounds 
        if(!$this->isEmpty() && (($index >= 0) && ($index < $this->size()))){ 
            $tmpNode = &$this->head; 
            for($i=0; $i<=$index; $i++) 
                    $tmpNode = &$tmpNode->getNext(); 

            return $tmpNode; 
        } 
        return FALSE; 
    } 
     
    /** 
     * Retrevies the object from the tail of the list 
     * @param $index A value between 0 and $this->size() 
     * @return The node || FALSE == index out of bounds 
     */ 
    function &getNodeReversed($index){ 
        //the list is not empty, and the index is not out of bounds 
        if(!$this->isEmpty() && (($index >= 0) && ($index < $this->size()))){ 
            $tmpNode = &$this->tail; 
            for($i=($this->size()-1); $i>=$index; $i--) 
                    $tmpNode = &$tmpNode->getPrevious(); 

            return $tmpNode; 
        } 
        return FALSE; 
         
    } 

    /** 
     * Returns the size of the list 
     * @return The number of elements in the list 
     */ 
      
    function size(){ 
        return $this->size; 
    } 
     
    /** 
     * Returns an iterator to use on the list 
     * @return An Iterator object, tha iterator starts on the list head. 
     */ 
    function iterator(){ 
        include_once("Iterator.php"); 
        return new Iterator(&$this->head, &$this); 
    } 
     
    /** 
     * Returns an ListIterator to use on the list 
     * @return An ListIterator object, tha iterator starts on the list head. 
     */ 
    function listIterator(){ 
        include_once("ListIterator.php"); 
        return new ListIterator($this->head, $this); 
    } 
     
    function incSize(){ 
        $this->size++; 
    } 
     
    function decSize(){ 
        $this->size--; 
    } 
    
    // Metodo aggiunto per debug
    
    public function __toString(){
    	$tmp = '';
    	for ($i = 0; $i < $this->size; $i++) {
    	 	$tmp .= $this->get($i);
    	}
    	return $tmp;
    }
} 
?>