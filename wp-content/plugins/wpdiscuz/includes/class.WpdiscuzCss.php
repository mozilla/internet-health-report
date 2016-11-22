<?php

if (!defined('ABSPATH')) {
    exit();
}

class WpdiscuzCss {

    private $optionsSerialized;
    private $helper;

    function __construct($optionsSerialized, $helper) {
        $this->optionsSerialized = $optionsSerialized;
        $this->helper = $helper;
    }

    /**
     * init wpdiscuz styles
     */
    public function initCustomCss() {
        global $post;
        if ($this->helper->isLoadWpdiscuz($post)) {
            ?>
<style type="text/css">#wpcomm .wc_new_comment{background:<?php echo $this->optionsSerialized->primaryColor; ?>;}#wpcomm .wc_new_reply{background:<?php echo $this->optionsSerialized->primaryColor; ?>;}#wpcomm .wc-form-wrapper{background:<?php echo isset($this->optionsSerialized->formBGColor)?$this->optionsSerialized->formBGColor:'#f9f9f9'; ?>;}#wpcomm select,#wpcomm input[type="text"],#wpcomm input[type="email"],#wpcomm input[type="url"],#wpcomm input[type="date"],#wpcomm input[type="color"]{border:<?php echo $this->optionsSerialized->inputBorderColor; ?> 1px solid;}#wpcomm .wc-comment .wc-comment-right{background:<?php echo $this->optionsSerialized->commentBGColor; ?>;}#wpcomm .wc-reply .wc-comment-right{background:<?php echo $this->optionsSerialized->replyBGColor; ?>;}#wpcomm .wc-comment-text{font-size:<?php echo isset($this->optionsSerialized->commentTextSize)?$this->optionsSerialized->commentTextSize:'14px'; ?>;color:<?php echo $this->optionsSerialized->commentTextColor; ?>;}<?php $blogRoles=$this->optionsSerialized->blogRoles;if(!$blogRoles){echo '.wc-comment-author a{color:#00B38F;} .wc-comment-label{background:#00B38F;}';}foreach($blogRoles as $role=>$color){echo '#wpcomm .wc-blog-'.$role.' > .wc-comment-right .wc-comment-author,#wpcomm .wc-blog-'.$role.' > .wc-comment-right .wc-comment-author a{color:'.$color.';}';echo '#wpcomm .wc-blog-'.$role.' > .wc-comment-left .wc-comment-label{background:'.$color.';}';}?>.wc-load-more-submit{border:1px solid <?php echo $this->optionsSerialized->inputBorderColor; ?>;}#wpcomm .wc-new-loaded-comment > .wc-comment-right{background:<?php echo $this->optionsSerialized->newLoadedCommentBGColor; ?>;}<?php echo stripslashes($this->optionsSerialized->customCss); ?>.wpdiscuz-front-actions{background:<?php echo isset($this->optionsSerialized->formBGColor)?$this->optionsSerialized->formBGColor:'#f9f9f9'; ?>;}.wpdiscuz-subscribe-bar{background:<?php echo isset($this->optionsSerialized->formBGColor)?$this->optionsSerialized->formBGColor : '#f9f9f9'; ?>;}.wpdiscuz-sort-buttons{color:<?php echo $this->optionsSerialized->buttonColor; ?>;}.wpdiscuz-sort-button{color:<?php echo $this->optionsSerialized->buttonColor; ?>; cursor:pointer;}.wpdiscuz-sort-button:hover{color:<?php echo $this->optionsSerialized->primaryColor; ?>;cursor:pointer;}.wpdiscuz-sort-button-active{color:<?php echo $this->optionsSerialized->primaryColor; ?>!important;cursor:default!important;}#wpcomm .page-numbers{color:<?php echo $this->optionsSerialized->commentTextColor; ?>;border:<?php echo $this->optionsSerialized->commentTextColor; ?> 1px solid;}#wpcomm span.current{background:<?php echo $this->optionsSerialized->commentTextColor; ?>;}#wpcomm .wpdiscuz-readmore{cursor:pointer;color:<?php echo $this->optionsSerialized->primaryColor; ?>;}<?php do_action('wpdiscuz_dynamic_css'); ?> #wpcomm .wpdiscuz-textarea-wrap{border:<?php echo $this->optionsSerialized->inputBorderColor; ?> 1px solid;} .wpd-custom-field .wcf-pasiv-star, #wpcomm .wpdiscuz-item .wpdiscuz-rating > label {color: <?php echo $this->optionsSerialized->ratingInactivColor; ?>;}#wpcomm .wpdiscuz-item .wpdiscuz-rating:not(:checked) > label:hover,.wpdiscuz-rating:not(:checked) > label:hover ~ label {   }#wpcomm .wpdiscuz-item .wpdiscuz-rating > input ~ label:hover, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:not(:checked) ~ label:hover ~ label, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:not(:checked) ~ label:hover ~ label{color: <?php echo $this->optionsSerialized->ratingHoverColor; ?>;} #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover, #wpcomm .wpdiscuz-item .wpdiscuz-rating > label:hover ~ input:checked ~ label, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:checked + label:hover ~ label, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label:hover ~ label, .wpd-custom-field .wcf-activ-star, #wpcomm .wpdiscuz-item .wpdiscuz-rating > input:checked ~ label{ color:<?php echo $this->optionsSerialized->ratingActivColor;; ?>;} #wpcomm .wc-cta-button:hover{border: 1px solid <?php echo $this->optionsSerialized->primaryColor; ?>!important; background:<?php echo $this->optionsSerialized->primaryColor; ?>!important; color:#fff!important;} #wpcomm .wc-cta-active{border: 1px solid <?php echo $this->optionsSerialized->primaryColor; ?>!important; background:<?php echo $this->optionsSerialized->primaryColor; ?>!important; color:#fff!important;}#wpcomm .wpf-cta:hover{color:#fff!important; background:<?php echo $this->optionsSerialized->primaryColor; ?>!important; border:1px solid <?php echo $this->optionsSerialized->primaryColor; ?>!important;}
        
#wpcomm .wpf-cta{ border: 1px solid <?php echo $this->optionsSerialized->buttonColor['shb'] ?>; color:<?php echo $this->optionsSerialized->buttonColor['vbc'] ?>; }
#wpcomm .wc-cta-button{ background:<?php echo $this->optionsSerialized->buttonColor['abb'] ?>; border:1px solid <?php echo $this->optionsSerialized->buttonColor['abb'] ?>; color:<?php echo $this->optionsSerialized->buttonColor['abc'] ?>;}
#wpcomm .wc-cta-button-x{ background:<?php echo $this->optionsSerialized->buttonColor['abb'] ?>; border:1px solid <?php echo $this->optionsSerialized->buttonColor['abb'] ?>; color:<?php echo $this->optionsSerialized->buttonColor['abc'] ?>;}
#wpcomm .wc-vote-link{border:1px solid <?php echo $this->optionsSerialized->buttonColor['vbb'] ?>; color:<?php echo $this->optionsSerialized->buttonColor['vbc'] ?>;}
#wpcomm .wc-vote-result{border-top: 1px solid <?php echo $this->optionsSerialized->buttonColor['vbb'] ?>; border-bottom: 1px solid <?php echo $this->optionsSerialized->buttonColor['vbb'] ?>; color:<?php echo $this->optionsSerialized->buttonColor['vbc'] ?>;}
#wpcomm .wc-vote-result.wc-vote-result-like{border:1px solid <?php echo $this->optionsSerialized->buttonColor['vbb'] ?>;}
#wpcomm .wc-vote-result.wc-vote-result-dislike{border:1px solid <?php echo $this->optionsSerialized->buttonColor['vbb'] ?>;}
</style>
			<?php
        }
    }

}
?>