<?php 

namespace Netpower\Momo\Model\Config\Source;

class ModeMomo implements \Magento\Framework\Option\ArrayInterface
{      
    /**
     * Options mode of Momo
     *
     * @return array
     */
    public function toOptionArray()
    {       
        $result =   [
                        [
                            'value' => "test",
                            'label' => "Test Mode"
                        ],
                        [
                            'value' => "production",
                            'label' => "Production Mode"
                        ]
                    ];

        return $result;
    }
}