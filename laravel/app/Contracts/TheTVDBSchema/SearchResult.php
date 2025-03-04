<?php

namespace App\Contracts\TheTVDBSchema;

/**
 * @note https://thetvdb.github.io/v4-api/#model-SearchResult
 */
class SearchResult extends BaseSchema
{
    /**
     * string
     */
    const objectID = 'objectID';

    /**
     * array
     */
    const aliases = 'aliases';

    /**
     * string
     */
    const country = 'country';

    /**
     * string
     */
    const id = 'id';

    /**
     * string
     */
    const image_url = 'image_url';

    /**
     * string
     */
    const name = 'name';

    /**
     * string
     */
    const first_air_time = 'first_air_time';

    /**
     * string
     */
    const overview = 'overview';

    /**
     * string
     */
    const primary_language = 'primary_language';

    /**
     * string
     */
    const primary_type = 'primary_type';

    /**
     * string
     */
    const status = 'status';

    /**
     * string
     */
    const type = 'type';

    /**
     * string
     */
    const tvdb_id = 'tvdb_id';

    /**
     * string
     */
    const year = 'year';

    /**
     * string
     */
    const slug = 'slug';

    /**
     * array
     */
    const overviews = 'overviews';

    /**
     * array
     */
    const translations = 'translations';

    /**
     * string
     */
    const network = 'network';

    /**
     * array
     */
    const remote_ids = 'remote_ids';

    /**
     * string
     */
    const thumbnail = 'thumbnail';
}
