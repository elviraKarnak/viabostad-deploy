 <?php 

  $textBlock = get_field('editors_block');

if( $textBlock){
  ?>
 
    <section class="default-content">
        <div class="container">
            <?php echo $textBlock; ?>
        </div>
   </section>
   <?php } ?>