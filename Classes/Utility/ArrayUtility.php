<?php
/**
 * Arrays utility
 *
 * @category   Extension
 * @package    Autoloader
 * @subpackage Utility
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */


namespace HDNET\Autoloader\Utility;

/**
 * Arrays utility
 *
 * @package    Autoloader
 * @subpackage Utility
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */
class ArrayUtility {

	/**
	 * Set a node in the array
	 *
	 * @param array $data
	 * @param array $array
	 *
	 * @see http://www.php.net/manual/de/function.array-walk-recursive.php#106340
	 */
	public static function setNodes(array $data, array &$array) {
		$separator = '|'; // set this to any string that won't occur in your keys
		foreach ($data as $name => $value) {
			if (strpos($name, $separator) === FALSE) {
				// If the array doesn't contain a special separator character, just set the key/value pair.
				// If $value is an array, you will of course set nested key/value pairs just fine.
				$array[$name] = $value;
			} else {
				// In this case we're trying to target a specific nested node without overwriting any other siblings/ancestors.
				// The node or its ancestors may not exist yet.
				$keys = explode($separator, $name);
				// Set the root of the tree.
				$opt_tree =& $array;
				// Start traversing the tree using the specified keys.
				while ($key = array_shift($keys)) {
					// If there are more keys after the current one.
					if ($keys) {
						if (!isset($opt_tree[$key]) || !is_array($opt_tree[$key])) {
							// Create this node if it doesn't already exist.
							$opt_tree[$key] = array();
						}
						// Redefine the "root" of the tree to this node (assign by reference) then process the next key.
						$opt_tree =& $opt_tree[$key];
					} else {
						// This is the last key to check, so assign the value.
						$opt_tree[$key] = $value;
					}
				}
			}
		}
	}

	/**
	 * Merge the Array Smart
	 *
	 * @param array $array1
	 * @param array $array2
	 *
	 * @return array
	 */
	public static function mergeRecursiveDistinct(array &$array1, array &$array2) {
		$merged = $array1;

		foreach ($array2 as $key => &$value) {
			if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
				$merged [$key] = self::mergeRecursiveDistinct($merged [$key], $value);
			} else {
				$merged [$key] = $value;
			}
		}

		return $merged;
	}

}