<?php
/**
 * Spreadr Frontend Functions
 *
 * General core functions available on both the front-end
 *
 * @author    Spreadr
 * @category  Core
 * @package   Spreadr/Functions
 * @version     0.0.1
 */

if (!defined('ABSPATH'))
{
    exit;
}

// add_action( 'woocommerce_single_product_summary', 'spreadr_button_on_product_page', 30 );
if (!empty(get_option('spreadr_custom_single_page')))
{

    add_filter('woocommerce_single_product_summary', 'spreadr_button_on_product_custom_single_page', 30, 2);
}
else
{
    add_filter('woocommerce_single_product_summary', 'spreadr_button_on_product_page', 10, 2);
}

function spreadr_button_on_product_page()
{

    global $product;
    global $woocommerce;

    if ($product)
    {
        global $product;
        $_product = wc_get_product(get_the_ID());

        $strProductSource = get_post_meta(get_the_ID() , 'product-source', true);

        #check product source spreadr
        if ($strProductSource == "spreadr")
        {

           
            
            #check add_to_cart enbale if yes than update product_type simple for add to cart
            $spreadrButtonType = (int)get_option('spreadr_button_type');
            $strButtonType = (int)get_post_meta(get_the_ID() , 'spreadr_product_button_type', true);
            if (isset($strButtonType) && $strButtonType != "")
            {
                $spreadrButtonType = $strButtonType;
            }

            if( $strButtonType != 1) {
              wp_set_object_terms( get_the_ID(), 'external', 'product_type' );
              remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
              remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            }


            //remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );

            //remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

            if ($spreadrButtonType == 1 || $spreadrButtonType == 2)
            {
                wp_set_object_terms(get_the_ID() , 'simple', 'product_type');

                //do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
                
            }

            #show default button
            if ($spreadrButtonType == 0 || $spreadrButtonType == 2)
            {

                $product_url = get_post_meta(get_the_ID() , 'spreadr-url', true);
                $button_text = $product->single_add_to_cart_text();
                $strProductTitle = get_post_meta(get_the_ID() , 'spreadr-title', true);
                $strSpreadrRegion = get_post_meta(get_the_ID() , 'spreadr-region', true);

                $strSpreadrTags = get_option('spreadr_tags');
                if (isset($strSpreadrTags[$strSpreadrRegion]))
                {
                    $strSpreadrTag = $strSpreadrTags[$strSpreadrRegion];
                }

                $spreadrurlparts = parse_url($product_url);

                parse_str($spreadrurlparts['query'], $spreadrquery);
                if (isset($spreadrquery['tag']))
                {

                    $strExternalLink = str_replace($spreadrquery['tag'], $strSpreadrTag, $product_url);
                    $product_url = "'" . esc_url($strExternalLink) . "'";

                }
                else
                {
                    $product_url = "'" . esc_url(get_post_meta(get_the_ID() , 'spreadr-url', true)) . "?tag=" . $strSpreadrTag . "'";
                }

                $strButtonText = get_post_meta(get_the_ID() , 'spreadr_product_button_text', true);

                do_action('woocommerce_after_add_to_cart_button');

                $strButtonText = get_option('spreadr_button_text');
?>

        <p class="cart">
          <a tag="<?php echo $strSpreadrTag ?>" spreadr_region="<?php echo $strSpreadrRegion; ?>" spreadr_product_title="<?php echo $strProductTitle; ?>" href="javascript:void(0);" rel="nofollow" onclick="SpreadrButtonClick(<?php echo $product_url ?> ,this)" class="single_add_to_cart_button button alt" ><?php echo esc_html($strButtonText); ?></a>
        </p>
        <?php do_action('woocommerce_after_add_to_cart_button');

            }

        }

    }
}

function spreadr_custom_button($link)
{

    global $product;
    global $woocommerce;

    $userSetting = array(
        'add_to_cart' => true,
        'deafult' => true
    );

    $strSpreadrTag = '';

    $strProductSource = get_post_meta(get_the_ID() , 'product-source', true);
    if ($strProductSource == "spreadr")
    {

        $spreadrButtonType = (int)get_option('spreadr_button_type');

        $strButtonType = (int)get_post_meta(get_the_ID() , 'spreadr_product_button_type', true);
        if (isset($strButtonType) && $strButtonType == 1)
        {
            $spreadrButtonType = 1;
        }
        else if (isset($strButtonType) && $strButtonType == 0)
        {
            $spreadrButtonType = 0;
        }

        wp_set_object_terms(get_the_ID() , 'external', 'product_type');

        if ($spreadrButtonType == 1)
        {
            wp_set_object_terms(get_the_ID() , 'simple', 'product_type');
            return $link;
        }
        else if ($spreadrButtonType == 2)
        {
            wp_set_object_terms(get_the_ID() , 'simple', 'product_type');
            echo $link;
        }

        if ($spreadrButtonType == 2 || $spreadrButtonType == 0)
        {

            $strProductTitle = get_post_meta(get_the_ID() , 'spreadr-title', true);
            $strSpreadrRegion = get_post_meta(get_the_ID() , 'spreadr-region', true);

            $strSpreadrTags = get_option('spreadr_tags');
            if (isset($strSpreadrTags[$strSpreadrRegion]))
            {
                $strSpreadrTag = $strSpreadrTags[$strSpreadrRegion];
            }

            $spreadrurl = get_post_meta(get_the_ID() , 'spreadr-url', true);
            $spreadrurlparts = parse_url($spreadrurl);
            if(isset($spreadrurlparts['query'])){
                parse_str($spreadrurlparts['query'], $spreadrquery);
            }
           
            if (isset($spreadrquery['tag']))
            {

                $strExternalLink = str_replace($spreadrquery['tag'], $strSpreadrTag, $spreadrurl);
                $strExternalLink = "'" . esc_url($strExternalLink) . "'";

            }
            else
            {
                $strExternalLink = "'" . esc_url(get_post_meta(get_the_ID() , 'spreadr-url', true)) . "?tag=" . $strSpreadrTag . "'";
            }

            $strButtonText = get_option('spreadr_button_text');

            $link = sprintf('<a tag="' . $strSpreadrTag . '" spreadr_region="' . $strSpreadrRegion . '" spreadr_product_title="' . $strProductTitle . '" rel="nofollow" href="javascript:void(0);"
            onclick ="SpreadrButtonClick(' . $strExternalLink . ',this);" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s" >%s</a>',
            //esc_url( $product->add_to_cart_url() ),
            esc_attr(isset($quantity) ? $quantity : 1) , esc_attr($product->get_id()) , esc_attr($product->get_sku()) , esc_attr(isset($class) ? $class : 'button product_type_external') , esc_html($strButtonText));
        }
    }

    return $link;
}

if (!empty(get_option('spreadr_custom_collection_page')))
{
    add_filter('woocommerce_loop_add_to_cart_link', 'spreadr_custom_collection_page_button', 10, 2);
}
else
{
    add_filter('woocommerce_loop_add_to_cart_link', 'spreadr_custom_button', 10, 2);
}

add_action('wp_footer', 'spreadrButton');

add_filter('woocommerce_product_tabs', 'spreadr_amazon_review_tab');
function spreadr_amazon_review_tab($tabs)
{
    global $post;
    $spreadr_asin = get_post_meta($post->ID, 'spreadr-asin', true);

    if (get_option('spreadr_is_review_on') && get_option('spreadr_review_display') == 'tab')
    {
        $tabs['spreadr_review_tab'] = array(
            'title' => __('Amazon Reviews', 'woocommerce') ,
            'priority' => 20,
            'callback' => 'spreadr_amazon_review_tab_content'
        );
    }

    return $tabs;
}

function spreadr_amazon_review_tab_content()
{
    global $post;
    if ($post)
    {
        $post_id = $post->ID;

        if (get_option('spreadr_is_review_on') && get_option('spreadr_review_display') == 'tab')
        {

            $spreadr_user_id = get_option('spreadr_review_userid');
            $spreadr_review_token = get_option('spreadr_review_token');
            $spreadr_asin = get_post_meta($post->ID, 'spreadr-asin', true);
            $review_url = SPREADR_REVIEW_URL . '?userid=' . $spreadr_user_id . '&token=' . $spreadr_review_token . '&asin=' . $spreadr_asin;

?>
      <script type="text/javascript">

        jQuery(function() {

          jQuery.ajax({url: "<?php echo $review_url ?>", success: function(result){
            if (result.data.iframeUrl) {
              jQuery("#tab-spreadr_review_tab").append('<iframe src="'+result.data.iframeUrl+'" class="SpreadrReviewFrame SpreadrCustomReviewCSS" width="100%" height="450" frameborder="0"></iframe>');
            }

             jQuery('.SpreadrReviewFrame').css({'border-color': '#ddd',
                                                     'border-width':'1px',
                                                     'border-style':'solid'});
          }});





        });
      </script>
      <?php
        }
    }

}

add_action('woocommerce_after_single_product_summary', 'spreadr_review_below_description', 20);

function spreadr_review_below_description()
{
    echo "<div id='spreadr_review'></div>";
    global $post;
    if ($post)
    {
        $post_id = $post->ID;

        if (get_option('spreadr_is_review_on') && get_option('spreadr_review_display') == 'description')
        {

            $spreadr_user_id = get_option('spreadr_review_userid');
            $spreadr_review_token = get_option('spreadr_review_token');
            $spreadr_asin = get_post_meta($post->ID, 'spreadr-asin', true);
            $review_url = SPREADR_REVIEW_URL . '?userid=' . $spreadr_user_id . '&token=' . $spreadr_review_token . '&asin=' . $spreadr_asin;

?>
      <script type="text/javascript">

        jQuery(function() {

          jQuery.ajax({url: "<?php echo $review_url ?>", success: function(result){
            if (result.data.iframeUrl) {
              jQuery("#spreadr_review").append('<iframe src="'+result.data.iframeUrl+'" width="100%" height="450" frameborder="0"></iframe>');
            }
          }});


        });
      </script>
      <?php
        }
    }
}

function spreadrButton()
{
    $strSpreadrTags = get_option('spreadr_tags');

?>

  <script type="text/javascript">

    var isSpreadrAnalytics  = "<?php echo get_option('spreadr_is_analytics'); ?>";
    var isSpreadrGoLocalize = "<?php echo get_option('spreadr_geo_localize'); ?>";
    var isFacebookPixel = "<?php echo get_option('is_facebook_pixel'); ?>";

    function SpreadrButtonClick( spreadrRedirectURL, instance ) {

       if( isSpreadrGoLocalize == '1') {
          var spreadrRegion = instance.getAttribute("spreadr_region").trim();
          var spreadrProductTitle = instance.getAttribute("spreadr_product_title").trim();
          spreadrFindlocation( spreadrRedirectURL , spreadrRegion ,spreadrProductTitle );
        } else {
          window.open( spreadrRedirectURL );
        }

        if( isSpreadrAnalytics == '1' ) {
          if( typeof ga !== "undefined" ) {
            ga("send", "event", { eventCategory: "Spreadr Link", eventAction: "Click", eventLabel:spreadrRedirectURL});
          }
        }

        if( isFacebookPixel == '1' ) {
          if(typeof fbq !== "undefined") {
            fbq("trackCustom", "SpreadrClick", {Amazonlink: spreadrRedirectURL});
          }
        }
    }

    var locationCountryCode = '';


    jQuery.ajax({
        url: "//extreme-ip-lookup.com/json",
        type: "POST",
        dataType: "jsonp",
        success: function(location) {
            if(location.countryCode == undefined){
                 jQuery.ajax({
                    url: "//api.wipmania.com/json/",
                    type: "POST",
                    dataType: "jsonp",
                    success: function(location) {
                    locationCountryCode = location.address.country_code.toLowerCase();
                      //localize_custom(location.address.country_code.toLowerCase());
                    }
                });
            } else {
                //localize_custom(location.countryCode.toLowerCase());
                 locationCountryCode = location.countryCode.toLowerCase();
            }

        }
    });


    function spreadrFindlocation( spreadrRedirectURL , spreadrRegion ,spreadrProductTitle ) {

      spreadrLocalize( locationCountryCode , spreadrRedirectURL , spreadrRegion , spreadrProductTitle );
    }

    function spreadrLocalize( country_code , defaultSpreadrRedirectURL , spreadr_region , spreadrProductTitle ) {
    //country_code = "ca";

     //$strSpreadrRegion = get_post_meta( get_the_ID(), 'spreadr-region', true );




    switch( country_code ) {
    case "us":
        if(spreadr_region != "com") {
          spreadrConvertlink("com","<?php echo isset($strSpreadrTags['com']) ? $strSpreadrTags['com'] : '' ?>",spreadrProductTitle );
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "ca":
        if(spreadr_region != "ca"){
          spreadrConvertlink("ca","<?php echo isset($strSpreadrTags['ca']) ? $strSpreadrTags['ca'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "gb":
        if(spreadr_region != "co.uk"){
          spreadrConvertlink("co.uk","<?php echo isset($strSpreadrTags['co.uk']) ? $strSpreadrTags['co.uk'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "in":
        if(spreadr_region != "in"){
          spreadrConvertlink("in","<?php echo isset($strSpreadrTags['in']) ? $strSpreadrTags['in'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "de":
        if(spreadr_region != "de"){
          spreadrConvertlink("de","<?php echo isset($strSpreadrTags['de']) ? $strSpreadrTags['de'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "fr":
        if(spreadr_region != "fr"){
          spreadrConvertlink("fr","<?php echo isset($strSpreadrTags['fr']) ? $strSpreadrTags['fr'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "es":
        if(spreadr_region != "es"){
          spreadrConvertlink("es","<?php echo isset($strSpreadrTags['es']) ? $strSpreadrTags['es'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "it":
        if(spreadr_region != "it"){
          spreadrConvertlink("it","<?php echo isset($strSpreadrTags['it']) ? $strSpreadrTags['it'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
     case "mx":
        if(spreadr_region != "com.mx"){
          spreadrConvertlink("com.mx","<?php echo isset($strSpreadrTags['com.mx']) ? $strSpreadrTags['com.mx'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "jp":
        if(spreadr_region != "com.jp"){
          spreadrConvertlink("co.jp","<?php echo isset($strSpreadrTags['co.jp']) ? $strSpreadrTags['co.jp'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "au":
        if(spreadr_region != "com.au"){
          spreadrConvertlink("com.au","<?php echo isset($strSpreadrTags['com.au']) ? $strSpreadrTags['com.au'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    case "br":
        if(spreadr_region != "com.br"){
          spreadrConvertlink("com.br","<?php echo isset($strSpreadrTags['com.br']) ? $strSpreadrTags['com.br'] : '' ?>",spreadrProductTitle);
        }else{
            spreadrRedirection( defaultSpreadrRedirectURL );
        }
        break;
    default:
      spreadrRedirection( defaultSpreadrRedirectURL );
    break;
  }
}

 function spreadrConvertlink( spreadr_region, aftag='',keywords='' ) {

    spreadrRedirectURL = "http://amazon." + spreadr_region + "/s/?field-keywords=" + keywords + "&tag=" + aftag;
    spreadrRedirection( spreadrRedirectURL );
 }

function spreadrRedirection( spreadrRedirectURL ){

 window.open( spreadrRedirectURL, '_blank');

}

 <?php echo stripslashes(get_option('spreadr_custom_javascript')); ?>

</script>

<?php
}

function spreadr_custom_collection_page_button($link)
{

    global $product;
    global $woocommerce;

    $strSpreadrTag = '';

    $strProductSource = get_post_meta(get_the_ID() , 'product-source', true);
    if ($strProductSource == "spreadr")
    {

        $spreadrButtonType = (int)get_option('spreadr_custom_button_type');

        $strButtonType = (int)get_post_meta(get_the_ID() , 'spreadr_product_button_type', true);
        if (isset($strButtonType) && $strButtonType == 1)
        {
            $spreadrButtonType = 1;
        }
        else if (isset($strButtonType) && $strButtonType == 0)
        {
            $spreadrButtonType = 0;
        }

        wp_set_object_terms(get_the_ID() , 'external', 'product_type');

        if ($spreadrButtonType == 1)
        {
            wp_set_object_terms(get_the_ID() , 'simple', 'product_type');
            return $link;
        }
        else if ($spreadrButtonType == 2)
        {
            wp_set_object_terms(get_the_ID() , 'simple', 'product_type');
            echo $link;
        }

        if ($spreadrButtonType == 2 || $spreadrButtonType == 0)
        {

            $strProductTitle = get_post_meta(get_the_ID() , 'spreadr-title', true);
            $strSpreadrRegion = get_post_meta(get_the_ID() , 'spreadr-region', true);

            $strSpreadrTags = get_option('spreadr_tags');
            if (isset($strSpreadrTags[$strSpreadrRegion]))
            {
                $strSpreadrTag = $strSpreadrTags[$strSpreadrRegion];
            }

            $spreadrurl = get_post_meta(get_the_ID() , 'spreadr-url', true);
            $spreadrurlparts = parse_url($spreadrurl);
            
            if(isset($spreadrurlparts['query'])){
                parse_str($spreadrurlparts['query'], $spreadrquery);
            }
            
            if (isset($spreadrquery['tag']))
            {

                $strExternalLink = str_replace($spreadrquery['tag'], $strSpreadrTag, $spreadrurl);
                $strExternalLink = "'" . esc_url($strExternalLink) . "'";
            }
            else
            {
                $strExternalLink = "'" . esc_url(get_post_meta(get_the_ID() , 'spreadr-url', true)) . "?tag=" . $strSpreadrTag . "'";
            }

            $strButtonText = get_option('spreadr_button_text');

            $link = sprintf('<a tag="' . $strSpreadrTag . '" spreadr_region="' . $strSpreadrRegion . '" spreadr_product_title="' . $strProductTitle . '" rel="nofollow" href="javascript:void(0);"
            onclick ="SpreadrButtonClick(' . $strExternalLink . ',this);" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s" >%s</a>',
            //esc_url( $product->add_to_cart_url() ),
            esc_attr(isset($quantity) ? $quantity : 1) , esc_attr($product->get_id()) , esc_attr($product->get_sku()) , esc_attr(isset($class) ? $class : 'button product_type_external') , esc_html($strButtonText));
        }
    }

    return $link;
}

function spreadr_button_on_product_custom_single_page()
{

    global $product;
    global $woocommerce;

    if ($product)
    {
        global $product;
        $_product = wc_get_product(get_the_ID());
        $strSpreadrTag = '';
        $strProductSource = get_post_meta(get_the_ID() , 'product-source', true);

        #check product source spreadr
        if ($strProductSource == "spreadr")
        {

            #check add_to_cart enbale if yes than update product_type simple for add to cart
            $spreadrButtonType = (int)get_option('spreadr_button_type');
            $strButtonType = (int)get_post_meta(get_the_ID() , 'spreadr_product_button_type', true);
            if (isset($strButtonType) && $strButtonType != "")
            {
                $spreadrButtonType = $strButtonType;
            }

            if ($strButtonType != 1)
            {
                wp_set_object_terms(get_the_ID() , 'external', 'product_type');
                remove_action('woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30);

                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            }

            //remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
            if ($spreadrButtonType == 1 || $spreadrButtonType == 2)
            {
                wp_set_object_terms(get_the_ID() , 'simple', 'product_type');

                //do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
                
            }
            #show default button
            if ($spreadrButtonType == 0 || $spreadrButtonType == 2)
            {

                $product_url = get_post_meta(get_the_ID() , 'spreadr-url', true);
                $button_text = $product->single_add_to_cart_text();
                $strProductTitle = get_post_meta(get_the_ID() , 'spreadr-title', true);
                $strSpreadrRegion = get_post_meta(get_the_ID() , 'spreadr-region', true);

                $strSpreadrTags = get_option('spreadr_tags');
                if (isset($strSpreadrTags[$strSpreadrRegion]))
                {
                    $strSpreadrTag = $strSpreadrTags[$strSpreadrRegion];
                }

                $spreadrurlparts = parse_url($product_url);
                
                if(isset($spreadrurlparts['query'])){
                    parse_str($spreadrurlparts['query'], $spreadrquery);
                }
                
                if (isset($spreadrquery['tag']))
                {

                    $strExternalLink = str_replace($spreadrquery['tag'], $strSpreadrTag, $product_url);
                    $product_url = "'" . esc_url($strExternalLink) . "'";

                }
                else
                {
                    $product_url = "'" . esc_url(get_post_meta(get_the_ID() , 'spreadr-url', true)) . "?tag=" . $strSpreadrTag . "'";
                }

                $strButtonText = get_post_meta(get_the_ID() , 'spreadr_product_button_text', true);

                $strButtonText = (empty($strButtonText) ? get_option('spreadr_button_text') : $strButtonText);

                do_action('woocommerce_after_add_to_cart_button'); ?>

        <p class="cart">
          <a tag="<?php echo $strSpreadrTag ?>" spreadr_region="<?php echo $strSpreadrRegion; ?>" spreadr_product_title="<?php echo $strProductTitle; ?>" href="javascript:void(0);" rel="nofollow" onclick="SpreadrButtonClick( <?php echo $product_url ?>,this)" class="single_add_to_cart_button button alt" ><?php echo esc_html($strButtonText); ?></a>
        </p>
        <?php do_action('woocommerce_after_add_to_cart_button');

            }

        }

    }
}

function spreadr_exit_popup()
{
    echo '<script type="text/javascript">
 ' . get_option('spreadr_exit_popup') . '
</script>';

}
add_action('wp_footer', 'spreadr_exit_popup');

