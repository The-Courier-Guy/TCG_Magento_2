<?php

namespace AppInlet\TheCourierGuy\Controller\Index;

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;

class Updatequote implements ActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var PageFactory
     */
    protected $pageResultFactory;

    /**
     * @param RequestInterface $request
     * @param Cart $cart
     * @param JsonFactory $jsonResultFactory
     * @param PageFactory $pageResultFactory
     */
    public function __construct(
        RequestInterface $request,
        Cart $cart,
        JsonFactory $jsonResultFactory,
        PageFactory $pageResultFactory
    ) {
        $this->request = $request;
        $this->cart = $cart;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->pageResultFactory = $pageResultFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $post = $this->request->getPostValue();
        $jsonResult = $this->jsonResultFactory->create();

        if (isset($post['place_id'])) {
            $quote = $this->cart->getQuote();
            $quote->setCourierguyPlaceId($post['place_id']);
            try {
                $quote->save();
                return $jsonResult->setData(['success' => true, 'message' => 'Place Post Success']);
            } catch (Exception $ex) {
                return $jsonResult->setData(['success' => false, 'message' => 'Place Post Failed']);
            }
        }

        return $this->pageResultFactory->create();
    }
}
