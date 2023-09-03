<?php
$page_title = "My Orders";
include 'includes/header.php';
?>

<div class="page-wrapper">

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 style="display:inline;" class="page-title text-truncate text-dark font-weight-medium mb-1">My Orders
                </h3>

                <?php include_once 'template/default/composed/filters/products_orders.php'; ?>


                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href=""><?= $note; ?></a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-right">
                <?php include_once 'template/default/composed/filters/products_orders.php'; ?>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <div class="col-12">

            <table class="table table-striped">
                <?php
                $i = 1;
                foreach ($orders as $order) : ?>
                    <tr>
                        <div class="alert bg-white custom-shadow  alert-dismissible mb-2 " role="alert">
                            <!--<span style="margin-right:2px;"><?= $i++; ?>)</span>-->
                            <!-- <strong> Items x Qty: <?= $order->total_item(); ?> x <?= $order->total_qty(); ?></strong> -->
                            <strong> <?= $order->TransactionID; ?></strong><br>
                            <small class="float-"><?= date("M j Y h:ia", strtotime($order->created_at)); ?></small>
                            <div style="position: absolute;top: 10px;right: 25px;">
                                <!-- <small>Price: </small><?= $currency; ?><?= $this->money_format($order['amount_payable']); ?><br> -->

                                <small class="float-"><?= $order->paymentstatus; ?> <?= $order->DisplayableStatus; ?> </small><br>

                                <div class="btn-group btn-group-sm text-small">
                                    <a href="<?= $domain; ?>/user/order/<?= $order->id; ?>" class="badge badge-light">Open</a>
                                    <!-- <a href="<?= $domain; ?>/" class=" badge badge-light">Cancel</a> -->
                                </div>
                            </div>
                        </div>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

            <?php if (count($orders) == 0) : ?>
                <div class="alert alert-white text-center">
                    When you buy any product, it will show here.
                </div>
            <?php endif; ?>
            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>
        </div>



    </div>

    <?php include 'includes/footer.php'; ?>