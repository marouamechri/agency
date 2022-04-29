<?php
namespace App\Data;

class SearchData
{
    /**
     * @var string
     */
    public $cle;


     /**
     * @var null|integer
     */
     public $prixmin;

     /**
     * @var null|integer
     */
     public $prixmax;
     /**
     * @var null|integer
     */
     public $surfacemax;
 /**
     * @var null|integer
     */
    public $surfacemin;
     /**
     * @var null|integer
     */
     public $nbpiecemin;

     /**
     * @var null|integer
     */
     public $nbpiecemax;

     public function __toString()
     {
         return '';
     }

     /**
      * Get the value of surfacemax
      *
      * @return  null|integer
      */ 
     public function getSurfacemax()
     {
          return $this->surfacemax;
     }

     /**
      * Set the value of surfacemax
      *
      * @param  null|integer  $surfacemax
      *
      * @return  self
      */ 
     public function setSurfacemax($surfacemax)
     {
          $this->surfacemax = $surfacemax;

          return $this;
     }
}
?>