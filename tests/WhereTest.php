<?php

namespace AnourValar\EloquentSerialize\Tests;

use AnourValar\EloquentSerialize\Tests\Models\User;
use AnourValar\EloquentSerialize\Tests\Models\UserPhone;

class WhereTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testSimple()
    {
        // One column
        $this->compare(User::where('id', '=', '1'));

        // Two columns
        $this->compare(User::where('id', '=', '1')->orWhere('id', '=', '2'));
    }

    /**
     * @return void
     */
    public function testExpression()
    {
        // Raw
        $this->compare(
            User::whereRaw('(id = 5 or (SELECT COUNT(*) FROM user_phones WHERE user_id = users.id) > 1)')
        );

        // 1 level
        $this->compare(
            User::where(function ($query)
            {
                $query->where('id', '=', '1')->orWhere('id', '=', 2);
            })
        );

        // 2 levels
        $this->compare(
            User::where(function ($query)
            {
                $query
                    ->where('id', '=', '1')
                    ->orWhere(function ($query)
                    {
                        $query->where('id', '=', '2')->where('title', '!=', 'admin');
                    });
            })
        );

        // 3 levels
        $this->compare(
            User::where(function ($query)
            {
                $query
                    ->where('id', '=', '1')
                    ->orWhere(function ($query)
                    {
                        $query
                            ->where('id', '=', '2')
                            ->orWhere(function ($query)
                            {
                                $query
                                    ->where('title', '!=', 'admin')
                                    ->orWhere('id', '=', '3');
                            });
                    });
            })
        );
    }

    /**
     * @return void
     */
    public function testHas()
    {
        // has
        $this->compare(User::has('userPhones'));

        // whereHas, 1 level
        $this->compare(
            User::whereHas('userPhones', function ($query)
            {
                $query->where('created_at', '>=', '2020-01-01');
            })
        );

        // whereHas, X levels
        $this->compare(
            User::where(function ($query)
            {
                $query->whereHas('userPhones', function ($query)
                {
                    $query->where(function ($query)
                    {
                        $query
                            ->where('created_at', '>=', '2020-01-01')
                            ->orWhere('id', '=', '1');
                    });
                });
            })
        );
    }

    /**
     * @return void
     */
    public function testDoesnthave()
    {
        // doesnthave
        $this->compare(User::doesnthave('userPhones'));

        // whereDoesnthave
        $this->compare(
            User::whereDoesnthave('userPhones', function ($query)
            {
                $query->where('created_at', '>=', '2020-01-01');
            })
        );

        // whereDoesnthave, X levels
        $this->compare(
            User::where(function ($query)
            {
                $query->whereDoesnthave('userPhones', function ($query)
                {
                    $query->where(function ($query)
                    {
                        $query
                            ->where('created_at', '>=', '2020-01-01')
                            ->orWhere('id', '=', '1');
                    });
                });
            })
        );
    }
}
