<?php

namespace Orth\IndexBundle\Elastica;

use FOS\ElasticaBundle\Finder\TransformedFinder;

class ExtendedTransformedFinder extends TransformedFinder
{

    /**
     * @return \Elastica\SearchableInterface
     */
    public function getSearch()
    {
        return $this->searchable;
    }

}
