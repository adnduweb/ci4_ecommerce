<!-- Account Login -->
<div class="icon_user_info">
    <a href="sign-in" title="<?= lang('Front_default.Identifiez-vous'); ?>" rel="nofollow">
        <i class="icon icon-user"></i>
        <span class="hidden"><?= lang('Front_default.Connexion'); ?></span>
    </a>
</div>
<!-- End Account Login -->

<!-- Shopping Cart -->
<div class="blockcart cart-preview">
    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
            <a href="/order/cart" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true"> <span class="glyphicon glyphicon-shopping-cart"></span> 7 - <?= lang('Front_default.Items'); ?><span class="caret"></span></a>
            <ul class="dropdown-menu dropdown-cart" role="menu">
                <li>
                    <span class="item">
                        <span class="item-left">
                            <img src="http://lorempixel.com/50/50/" alt="" />
                            <span class="item-info">
                                <span>Item name</span>
                                <span>23$</span>
                            </span>
                        </span>
                        <span class="item-right">
                            <button class="btn btn-xs btn-danger pull-right">x</button>
                        </span>
                    </span>
                </li>
                <li>
                    <span class="item">
                        <span class="item-left">
                            <img src="http://lorempixel.com/50/50/" alt="" />
                            <span class="item-info">
                                <span>Item name</span>
                                <span>23$</span>
                            </span>
                        </span>
                        <span class="item-right">
                            <button class="btn btn-xs btn-danger pull-right">x</button>
                        </span>
                    </span>
                </li>
                <li>
                    <span class="item">
                        <span class="item-left">
                            <img src="http://lorempixel.com/50/50/" alt="" />
                            <span class="item-info">
                                <span>Item name</span>
                                <span>23$</span>
                            </span>
                        </span>
                        <span class="item-right">
                            <button class="btn btn-xs btn-danger pull-right">x</button>
                        </span>
                    </span>
                </li>
                <li>
                    <span class="item">
                        <span class="item-left">
                            <img src="http://lorempixel.com/50/50/" alt="" />
                            <span class="item-info">
                                <span>Item name</span>
                                <span>23$</span>
                            </span>
                        </span>
                        <span class="item-right">
                            <button class="btn btn-xs btn-danger pull-right">x</button>
                        </span>
                    </span>
                </li>
                <li class="divider"></li>
                <li><a class="text-center" href="">View Cart</a></li>
            </ul>
        </li>
    </ul>
</div>
<!-- End Shopping Cart -->