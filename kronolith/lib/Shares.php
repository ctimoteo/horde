<?php
/**
 * Share wrapper to simplify listing shares.
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author Michael J Rubinsky <mrubinsk@horde.org>
 * @copyright 2015 Horde LLC (http://www.horde.org/)
 * @category Horde
 * @license  http://www.horde.org/licenses/gpl GPL
 * @package  Kronolith
 */
/**
 * @author Michael J Rubinsky <mrubinsk@horde.org>
 * @copyright 2015 Horde LLC (http://www.horde.org/)
 * @category Horde
 * @license  http://www.horde.org/licenses/gpl GPL
 * @package  Kronolith
 */
class Kronolith_Shares
{
    /**
     * Composed share object.
     *
     * @var Horde_Core_Share_Driver
     */
    protected $_shares;

    public function __construct(Horde_Core_Share_Driver $shares)
    {
        $this->_shares = $shares;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_shares, $method), $args);
    }

    /**
     * Returns an array of all shares that $userid has access to. By default
     * returns only normal calendar shares, and not resource shares. Set
     * $params['attributes']['type'] = Kronolith::SHARE_TYPE_RESOURCE to return
     * resources shares.
     *
     * @param string $userid  The userid of the user to check access for. An
     *                        empty value for the userid will only return shares
     *                        with guest access.
     * @param array $params   Additional parameters for the search.
     *<pre>
     *  'perm'        Require this level of permissions. Horde_Perms constant.
     *  'attributes'  Restrict shares to these attributes. A hash or username.
     *  'from'        Offset. Start at this share
     *  'count'       Limit.  Only return this many.
     *  'sort_by'     Sort by attribute.
     *  'direction'   Sort by direction.
     *</pre>
     *
     * @return array  The shares the user has access to.
     */
    public function listShares($userid, array $params = array())
    {
        $attributes = array('type' => Kronolith::SHARE_TYPE_USER);
        if (!empty($params['attributes']) && !is_array($params['attributes'])) {
                $attributes['owner'] = $params['attributes'];
                $params['attributes'] = array();
        } elseif (empty($params['attributes'])) {
            $params['attributes'] = array();
        }
        $attributes = array_merge($attributes, $params['attributes']);
        $params['attributes'] = $attributes;

        return $this->_shares->listShares($userid, $params);
    }

    /**
     * Returns a new share object. By default, creates a new user calendar share
     * and not a resource share. To create a resource share, calling code must
     * explicitly set the 'type' share attribute to
     * Kronolith::SHARE_TYPE_RESOURCE
     *
     * @param string $owner           The share owner name.
     * @param string $share_name      The share's name.
     * @param string $name_attribute  The name displayed to the user.
     *
     * @return Horde_Share_Object  A new share object.
     * @throws Horde_Share_Exception
     */
    public function newShare($owner, $share_name = '', $name_attribute = '')
    {
        $share = $this->_shares->newShare($owner, $share_name, $name_attribute);
        $share->set('type', Kronolith::SHARE_TYPE_USER);

        return $share;
    }

}