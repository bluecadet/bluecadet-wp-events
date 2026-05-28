import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { store as editorStore } from '@wordpress/editor';
import { dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

export const REST_NAMESPACE = 'bc-events/v1';

/** META KEY NAMESPACING */
export const META_SCOPE = 'bc-events/meta-keys';
export const META_KEY = 'meta-keys';

/**
 * Set Key values
 * 
 * todo: normalize data
 */
export function setKeys(value) {
  dispatch(preferencesStore).set(META_SCOPE, META_KEY, value);
}


/**
 * Load keys from preferencesStore
 */
export function loadKeys() {
  const value = useSelect(
    select => select(preferencesStore).get(META_SCOPE, META_KEY),
    [META_KEY]
  );

  return value;
}

/**
 * Get Key
 * 
 * @param {Object} keys - Object of keys
 * @param {string} keyName - Name of the key to retrieve
 */
export function getKey( keyName, keys = false ) {
  if ( ! keys ) {
    keys = loadKeys();
  }

  return keys[ keyName ] || null;
}


export function getPostType() {
  const postType = useSelect(
    ( select ) => select( editorStore ).getCurrentPostType(),
    []
  );

  return postType;
}



export function getStore() {
  const keys = loadKeys();
  const postType = getPostType();
  const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

  return {
    keys,
    postType,
    meta,
    setMeta
  }
}