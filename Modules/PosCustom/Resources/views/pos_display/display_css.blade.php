<style>
.slider1{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border-radius: 1rem; 
}
	
.slider img{ 
   max-width: 100%; 
   height: calc(100vh - 63px); /*size slider*/
}

.slideshow{
    display: flex;
    transform: translate3d(0, 0, 0);
    transition: all 9000ms; 
    animation-name: autoplay;
    animation-duration: 90s;
    animation-direction: alternate;
    animation-fill-mode: forwards;
    animation-iteration-count: infinite;
}

.item-slide{
    position: relative;
    display: flex;
    width: 100%;
    flex-direction: column;
    flex-shrink: 0;
    flex-grow: 0;
}

.pagination{
    position: absolute;
    bottom: 20px;
    left: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
}
.pag-item{
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 2px solid #CCC;
    width: 16px;
    height: 16px;
    border-radius: 5px;
    overflow: hidden;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.5);
    margin: 0 10px;
    text-align: center;
    transition: all 800ms;
}
.pag-item:hover{ transform: scale(2); }
.pag-item img{
    display: inline-block;
    max-width: none;
    height: 100%;
    transform: scale(1);
    opacity: 0;
    transition: all 300ms;
}
.pag-item:hover img{
    opacity: 1;
}
/* END*/
   
.banner {
       
      background: url(/uploads/login-bgs/1.jpg); 
       /* background: url(<?= gen_rnd_bg(); ?>); */
       background-size: cover;
       background-position: center;
       background-repeat: no-repeat;
       
   }
.heading-empty {
   flex-direction: column;  
   align-items: center;
   text-align: center;
   font-size: 24px;
   font-weight: bold;
   margin-bottom: 20px;
   }

.table-styled {
      border-radius: 2rem;
      border: 3px solid white;

      /*Other functions*/
      /* background-color: aquamarine; */
      height: calc(100vh - 150px); /*size*/
      overflow-x: auto;
      overflow-y: auto;
      text-align: center;
   }

.table-styled table {
      border-top: hidden;
      border-left: hidden;
      border-bottom: hidden;
      border-right: hidden;
      border-collapse: collapse;
   }

.table-styled table thead {
      position: sticky;
      top: 0;
      border-bottom: 2px solid #ccc;
   }
.table-styled table tfoot {
      position: sticky; 
      bottom: 0;
      border-bottom: 2px solid #ccc;
   }

.table-styled table thead tr,
.table-styled table tfoot tr {
      background-color: lightgray; 
      font-weight: bold;
      color: rgb(0, 0, 0);
      /* border-top: 2px solid #dee2e6; */
      text-align: center;
   }

.table-styled table th {
      text-align: center;
   }

.table-styled table th,
.table-styled table td {
      border: 10px solid white;
      padding: 1rem 2rem;
   }

@media (max-width: 768px) {
   /*Under constrution*/
   }

.custom-footer {
   background-color: #f8f9fa;
   padding: 10px 0;
   box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
   display: flex;
   justify-content: space-around;
   align-items: center;
   flex-wrap: wrap;
   }
.footer-item-total {
   text-align: center;
   padding: 5px 10px;
   font-weight: bold;
   border: 1px solid #dee2e6;
   }
.footer-item-total:first-child {
   border-left: none;
   }
   
   /*@keyframes I must be create dynamic using php*/
   /*
   @keyframes autoplay{
    20%{ transform: translate3d(calc(-100% * 0), 0, 0); }
    40%{ transform: translate3d(calc(-100% * 1), 0, 0); }
    60%{ transform: translate3d(calc(-100% * 2), 0, 0); }
    80%{ transform: translate3d(calc(-100% * 3), 0, 0); }
    100%{ transform: translate3d(calc(-100% * 4), 0, 0); }
   }
    */

</style>