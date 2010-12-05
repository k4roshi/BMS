<?php 
/** 
 * <p>Title: Double linked list</p> 
 * <p>Description: Implementation of a double linked list in PHP</p> 
 * @author Oddleif Halvorsen | leif-h@online.no 
 * @version 1.2 
 */ 
  
  
class ListNode{ 
    var $previousNode; 
    var $nextNode; //a reference to the next node 
    var $element; //the object || value for this node. 
     
    /** 
     * Constructs a ListNode. 
     * @param $element The object/value of the node 
     */ 
    function ListNode($element = NULL){ 
        $this->element = &$element; 
    } 
     
    /** 
     * Sets the reference to the previous node. 
     * @param &$node The previous node. 
     */ 
    function setPrevious($node){ 
        $this->previousNode = &$node; 
    } 
     
    /** 
     * Sets the reference to the next node. 
     * @param &$node The next node. 
     */ 
    function setNext($node){ 
        $this->nextNode = &$node; 
    } 
     
    /** 
     * Returns the reference to the previous node. 
     * @return The reference to the previous node. 
     */ 
    function &getPrevious(){ 
        return $this->previousNode; 
    } 

    /** 
     * Returns the reference to the next node. 
     * @return The reference to the next node. 
     */ 
    function &getNext(){ 
        return $this->nextNode; 
    } 

    /** 
     * Returns the object/value of the node 
     * @return The object/value of the node 
     */ 
    function getNodeValue(){ 
        return $this->element; 
    } 
} 
?>