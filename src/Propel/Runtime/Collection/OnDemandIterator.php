<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Propel\Runtime\Collection;

use Iterator;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Formatter\AbstractFormatter;
use Propel\Runtime\Propel;

/**
 * Class for iterating over a statement and returning one Propel object at a time
 *
 * @author Francois Zaninotto
 */
class OnDemandIterator implements Iterator
{
    /**
     * @var \Propel\Runtime\Formatter\ObjectFormatter
     */
    protected $formatter;

    /**
     * @var \Propel\Runtime\DataFetcher\DataFetcherInterface
     */
    protected $dataFetcher;

    protected $currentRow;

    protected $currentKey;

    protected $isValid;

    protected $enableInstancePoolingOnFinish;

    /**
     * @param \Propel\Runtime\Formatter\ObjectFormatter $formatter
     * @param \Propel\Runtime\DataFetcher\DataFetcherInterface $dataFetcher
     */
    public function __construct(AbstractFormatter $formatter, DataFetcherInterface $dataFetcher)
    {
        $this->currentKey = -1;
        $this->formatter = $formatter;
        $this->dataFetcher = $dataFetcher;
        $this->enableInstancePoolingOnFinish = Propel::disableInstancePooling();
    }

    /**
     * @return void
     */
    public function closeCursor()
    {
        $this->dataFetcher->close();
        if ($this->enableInstancePoolingOnFinish) {
            Propel::enableInstancePooling();
        }
    }

    /**
     * Returns the number of rows in the resultset
     * Warning: this number is inaccurate for most databases. Do not rely on it for a portable application.
     *
     * @return int Number of results
     */
    public function count()
    {
        return $this->dataFetcher->count();
    }

    // Iterator Interface

    /**
     * Gets the current Model object in the collection
     * This is where the hydration takes place.
     *
     * @see ObjectFormatter::getAllObjectsFromRow()
     *
     * @return \Propel\Runtime\ActiveRecord\ActiveRecordInterface
     */
    public function current()
    {
        return $this->formatter->getAllObjectsFromRow($this->currentRow);
    }

    /**
     * Gets the current key in the iterator
     *
     * @return string
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * Advances the cursor in the statement
     * Closes the cursor if the end of the statement is reached
     *
     * @return void
     */
    public function next()
    {
        $this->currentRow = $this->dataFetcher->fetch();
        $this->currentKey++;
        $this->isValid = (bool)$this->currentRow;
        if (!$this->isValid) {
            $this->closeCursor();
        }
    }

    /**
     * Initializes the iterator by advancing to the first position
     * This method can only be called once (this is a NoRewindIterator)
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return void
     */
    public function rewind()
    {
        // check that the hydration can begin
        if ($this->formatter === null) {
            throw new PropelException('The On Demand collection requires a formatter. Add it by calling setFormatter()');
        }
        if ($this->dataFetcher === null) {
            throw new PropelException('The On Demand collection requires a dataFetcher. Add it by calling setDataFetcher()');
        }
        if ($this->isValid !== null) {
            throw new PropelException('The On Demand collection can only be iterated once');
        }

        // initialize the current row and key
        $this->next();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (bool)$this->isValid;
    }
}
