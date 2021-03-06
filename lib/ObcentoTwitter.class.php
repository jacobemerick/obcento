<?php

require_once dirname(__FILE__) . '/ObcentoTwitterRequest.class.php';
require_once dirname(__FILE__) . '/ObcentoTwitterValidateInput.class.php';


/**
 * This is the client class for the Obcento Twitter
 * For licensing and examples:
 *
 * @see https://github.com/avalanche-development/obcento
 *
 * @author dave_kz (http://www.dave.kz/)
 * @version 1.0 (2013-03-18)
 */
class ObcentoTwitter 
{

	/**
	 * $result Holds the results of the twitter request
	 */
	private $result;

	/**
	 * $obcentoRequest Holds the request class after it is instantiated with the configs
	 */
	private $obcentoRequest;

	/**
	 * The constructor for this class
	 *
	 * @param	string	$consumer_key			from dev.twitter.com
	 * @param	string	$consumer_secret		from dev.twitter.com
	 * @param	string	$access_token			from dev.twitter.com
	 * @param	string	$access_token_secret	from dev.twitter.com
	 */
	public function __construct($consumer_key = null, $consumer_secret = null, $access_token = null, $access_token_secret = null)
	{
		// if any of the keys are not defined, try to load the config file
		if($consumer_key === null || $consumer_secret === null || $access_token === null || $access_token_secret === null)
			require_once self::get_config_filepath();
		
		if($consumer_key === null || $consumer_secret === null || $access_token === null || $access_token_secret === null)
			trigger_error('ObcentoTwitter OAuth credentials were not set in the constructor!');
		else
			$this->obcentoRequest = new ObcentoTwitterRequest($consumer_key, $consumer_secret, $access_token, $access_token_secret);
	}


	/**
	 * For you peeps out there who like to have cool-looking code
	 *
	 * @param	string	$consumer_key			from dev.twitter.com
	 * @param	string	$consumer_secret		from dev.twitter.com
	 * @param	string	$access_token			from dev.twitter.com
	 * @param	string	$access_token_secret	from dev.twitter.com
	 * @return	object	new ObcentoTwitter()
	 */
	public static function instance($consumer_key = null, $consumer_secret = null, $access_token = null, $access_token_secret = null)
	{
		return new ObcentoTwitter($consumer_key, $consumer_secret, $access_token, $access_token_secret);
	}

	/**
	 * Static method for pulling the default config file w/ params
	 *
	 * @return	string	config filepath
	 */
	private static function get_config_filepath()
	{
		$path = dirname(__FILE__);
		$path = explode(DIRECTORY_SEPARATOR, $path);
		array_pop($path);
		$path = implode(DIRECTORY_SEPARATOR, $path);
		$path .= DIRECTORY_SEPARATOR;
		
		return "{$path}config.php";
	}

	/**
	 * getMentionsTimeline:
	 * 
	 * Returns the 20 most recent mentions (tweets containing a users's @screen_name) for the authenticating user. The timeline returned is the
	 * equivalent of the one seen when you view your mentions on twitter.com. This method can only return up to 800 tweets.
	 * For an deeper look at these parameters, check out:
	 * @see https://dev.twitter.com/docs/api/1.1/get/statuses/mentions_timeline
	 *
	 * @param int $count	Specifies the number of tweets to try and retrieve, up to a maximum of 200. The value of count is best thought of as a 
	 *						limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. 
	 *						We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 
	 *						when using this API method. (Optional)
	 *
	 * @param int $since_id	Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number
	 *						of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the 
	 *						since_id will be forced to the oldest ID available. (Optional)
	 *
	 * @param int $max_id	Returns results with an ID less than (that is, older than) or equal to the specified ID. (Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *							numerical ID. Omit this parameter to receive the complete user object. (Optional)
	 *
	 * @param boolean $contributor_details	This parameter enhances the contributors element of the status response to include the screen_name of 
	 * 										the contributor. By default only the user_id of the contributor is included. (Optional)
	 *
	 * @param boolean $include_entities	The entities node will be disincluded when set to false. (Optional)
	 *
	 * @return object $this object for further manipulations
	 */
	public function getMentionsTimeline(
	               $count = NULL,
	            $since_id = NULL,
	              $max_id = NULL,
	           $trim_user = NULL,
	 $contributor_details = NULL,
	    $include_entities = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['count'] = $count;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['contributor_details'] = $contributor_details;
		$parameter_array['include_entities'] = $include_entities;
		
		$this->process_request('statuses/mentions_timeline', $parameter_array);
		
		return $this;
	}

	/**
	 * getUserTimeline:
	 *
	 * Returns a collection of the most recent Tweets posted by the user indicated by the screen_name. User timelines
	 * belonging to protected users may only be requested when the authenticated user either "owns" the timeline or is an approved follower 
	 * of the owner. The timeline returned is the equivalent of the one seen when you view a user's profile on twitter.com. This method can 
	 * only return up to 3,200 of a user's most recent Tweets. Native retweets of other statuses by the user is included in this total, 
	 * regardless of whether include_rts is set to false when requesting this resource.
 	 * For an deeper look at these parameters, check out:
	 * @see https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	 *
	 * @param string $screen_name	The screen name of the user for whom to return results for.(Optional)
	 *
	 * @param int $since_id	Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number
	 *						of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the 
	 *						since_id will be forced to the oldest ID available.(Optional)
	 *
	 * @param int $count	Specifies the number of tweets to try and retrieve, up to a maximum of 200. The value of count is best thought of as a 
	 *						limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. 
	 *						We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 
	 *						when using this API method. (Optional)
	 *
	 * @param int $max_id	Returns results with an ID less than (that is, older than) or equal to the specified ID.(Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *								numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @param boolean $exclude_replies	This parameter will prevent replies from appearing in the returned timeline. Using exclude_replies with 
	 *									the count parameter will mean you will receive up-to count tweets � this is because the count parameter
	 *									retrieves that many tweets before filtering out retweets and replies. This parameter is only supported 
	 *									for JSON and XML responses.
	 *
	 * @param boolean $contributor_details	This parameter enhances the contributors element of the status response to include the screen_name
	 *										of the contributor. By default only the user_id of the contributor is included.
	 *
	 * @param boolean $include_rts	When set to false, the timeline will strip any native retweets (though they will still count toward both 
	 *								the maximal length of the timeline and the slice selected by the count parameter). Note: If you're using 
	 *								the trim_user parameter in conjunction with include_rts, the retweets will still contain a full user object.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getUserTimeline(
		        $screen_name = NULL,
		           $since_id = NULL,
		              $count = NULL,
		             $max_id = NULL,
		          $trim_user = NULL,
		    $exclude_replies = NULL,
		$contributor_details = NULL,
		        $include_rts = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['screen_name'] = $screen_name;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['count'] = $count;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['exclude_replies'] = $exclude_replies;
		$parameter_array['contributor_details'] = $contributor_details;
		$parameter_array['include_rts'] = $include_rts;
		
		$this->process_request('statuses/user_timeline', $parameter_array);
		
		return $this;
	}

	/**
	 * getUserTimelineByUserId:
	 *
	 * Returns a collection of the most recent Tweets posted by the user indicated by the user_id. User timelines
	 * belonging to protected users may only be requested when the authenticated user either "owns" the timeline or is an approved follower 
	 * of the owner. The timeline returned is the equivalent of the one seen when you view a user's profile on twitter.com. This method can 
	 * only return up to 3,200 of a user's most recent Tweets. Native retweets of other statuses by the user is included in this total, 
	 * regardless of whether include_rts is set to false when requesting this resource.
	 * For an deeper look at these parameters, check out:
	 * @see https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	 *
	 * @param int $user_id	The ID of the user for whom to return results for.(Optional)
	 *
	 * @param int $since_id	Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number
	 *						of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the 
	 *						since_id will be forced to the oldest ID available.(Optional)
	 *
	 * @param int $count	Specifies the number of tweets to try and retrieve, up to a maximum of 200. The value of count is best thought of as a 
	 *						limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. 
	 *						We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 
	 *						when using this API method. (Optional)
	 *
	 * @param int $max_id	Returns results with an ID less than (that is, older than) or equal to the specified ID.(Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *							numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @param boolean $exclude_replies	This parameter will prevent replies from appearing in the returned timeline. Using exclude_replies with 
	 *									the count parameter will mean you will receive up-to count tweets � this is because the count parameter
	 *									retrieves that many tweets before filtering out retweets and replies. This parameter is only supported 
	 *									for JSON and XML responses.
	 *
	 * @param boolean $contributor_details	This parameter enhances the contributors element of the status response to include the screen_name
	 *										of the contributor. By default only the user_id of the contributor is included.
	 *
	 * @param boolean $include_rts	When set to false, the timeline will strip any native retweets (though they will still count toward both 
	 *								the maximal length of the timeline and the slice selected by the count parameter). Note: If you're using 
	 *								the trim_user parameter in conjunction with include_rts, the retweets will still contain a full user object.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getUserTimelineByUserId(
		            $user_id,
		           $since_id = NULL,
		              $count = NULL,
		             $max_id = NULL,
		          $trim_user = NULL,
		    $exclude_replies = NULL,
		$contributor_details = NULL,
		        $include_rts = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['user_id'] = $user_id;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['count'] = $count;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['exclude_replies'] = $exclude_replies;
		$parameter_array['contributor_details'] = $contributor_details;
		$parameter_array['include_rts'] = $include_rts;
		
		$this->process_request('statuses/user_timelin', $parameter_array);
		
		return $this;
	}

	/**
	 * getHomeTimeline:
	 * 
	 * Returns a collection of the most recent Tweets and retweets posted by the authenticating user and the users they follow. The home 
	 * timeline is central to how most users interact with the Twitter service. Up to 800 Tweets are obtainable on the home timeline. It 
	 * is more volatile for users that follow many users or follow users who tweet frequently.
	 * For an deeper look at these parameters, check out:
	 * @see https://dev.twitter.com/docs/api/1.1/get/statuses/home_timeline
	 *
	 * @param int !count	Specifies the number of tweets to try and retrieve, up to a maximum of 200. The value of count is best thought of as a 
	 *						limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. 
	 *						We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 
	 *						when using this API method. (Optional)
	 *
	 * @param int $since_id	Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number
	 *						of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the 
	 *						since_id will be forced to the oldest ID available.(Optional)
	 *
	 * @param int $max_id	Returns results with an ID less than (that is, older than) or equal to the specified ID.(Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *								numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @param boolean $exclude_replies	This parameter will prevent replies from appearing in the returned timeline. Using exclude_replies with 
	 *									the count parameter will mean you will receive up-to count tweets � this is because the count parameter
	 *									retrieves that many tweets before filtering out retweets and replies. This parameter is only supported 
	 *									for JSON and XML responses.
	 *
	 * @param boolean $contributor_details	This parameter enhances the contributors element of the status response to include the screen_name
	 *										of the contributor. By default only the user_id of the contributor is included.
	 *
	 * @param boolean $include_entities	The entities node will be disincluded when set to false.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getHomeTimeline(
		              $count = NULL,
		           $since_id = NULL,
		             $max_id = NULL,
		          $trim_user = NULL,
		    $exclude_replies = NULL,
		$contributor_details = NULL,
		   $include_entities = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['count'] = $count;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['exclude_replies'] = $exclude_replies;
		$parameter_array['contributor_details'] = $contributor_details;
		$parameter_array['include_entities'] = $include_entities;
		
		$this->process_request('statuses/home_timeline', $parameter_array);
		
		return $this;
	}

	/**
	 * getRetweetsOfMe:
	 *
	 * Returns the most recent tweets authored by the authenticating user that have been retweeted by others. This timeline is a subset 
	 * of the user's GET statuses/user_timeline.
	 * For an deeper look at these parameters, check out:
	 * @see https://dev.twitter.com/docs/api/1.1/get/statuses/retweets_of_me
	 *
	 * @param int $count	Specifies the number of tweets to try and retrieve, up to a maximum of 200. The value of count is best thought of as a 
	 *						limit to the number of tweets to return because suspended or deleted content is removed after the count has been applied. 
	 *						We include retweets in the count, even if include_rts is not supplied. It is recommended you always send include_rts=1 
	 *						when using this API method. (Optional)
	 *
	 * @param int $since_id	Returns results with an ID greater than (that is, more recent than) the specified ID. There are limits to the number
	 *						of Tweets which can be accessed through the API. If the limit of Tweets has occured since the since_id, the 
	 *						since_id will be forced to the oldest ID available.(Optional)
	 *
	 * @param int $max_id	Returns results with an ID less than (that is, older than) or equal to the specified ID.(Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *								numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @param boolean $include_entities	The entities node will be disincluded when set to false.
	 *
	 * @param boolean $include_user_entities	The user entities node will be disincluded when set to false.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getRetweetsOfMe(
		                $count = NULL,
		             $since_id = NULL,
		               $max_id = NULL,
		            $trim_user = NULL,
		     $include_entities = NULL,
		$include_user_entities = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['count'] = $count;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['include_entities'] = $include_entities;
		$parameter_array['include_user_entities'] = $include_user_entities;
		
		$this->process_request('statuses/retweets_of_me', $parameter_array);
		
		return $this;
	}

	/**
	 * getRetweetsByTweet
	 *
	 * Returns up to 100 of the first retweets of a given tweet.
	 *
	 *
	 * @param int $id	The numerical ID of the desired status. (Required)
	 *
	 * @param int $count	Specifies the number of records to retrieve. Must be less than or equal to 100. (Optional)
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *								numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @return object $this object for further manipulations
	 */
	public function getRetweetsByTweet(
						   $id		 ,
		                $count = NULL,
		            $trim_user = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['count'] = $count;
		$parameter_array['trim_user'] = $trim_user;
		
		$this->process_tweet_request('statuses/retweets', $id, $parameter_array);
		
		return $this;
	}

	/**
	 * getSingleTweetById
	 *
	 * Returns a single Tweet, specified by the id parameter. The Tweet's author will also be embedded within the tweet.
	 *
	 * @param int $id	The numerical ID of the desired Tweet.
	 *
	 * @param boolean $trim_user	When set to true, each tweet returned in a timeline will include a user object including only the status authors
	 *								numerical ID. Omit this parameter to receive the complete user object.(Optional)
	 *
	 * @param boolean $include_my_retweet	When set to either true, t or 1, any Tweets returned that have been retweeted by the 
	 *										authenticating user will include an additional current_user_retweet node, containing 
	 *										the ID of the source status for the retweet.
	 *
	 * @param boolean $include_user_entities	The user entities node will be disincluded when set to false.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getSingleTweetById(
						   $id		 ,
		            $trim_user = NULL,
		   $include_my_retweet = NULL,
		     $include_entities = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['trim_user'] = $trim_user;
		$parameter_array['include_my_retweet'] = $include_my_retweet;
		$parameter_array['include_entities'] = $include_entities;
		
		$this->process_tweet_request('statuses/show', $id, $parameter_array);
		
		return $this;
	}

	/**
	 * getOEmbedById:
	 *
	 * Returns information allowing the creation of an embedded representation of a Tweet on third party sites. See the oEmbed
	 * specification for information about the response format.
	 *
	 * While this endpoint allows a bit of customization for the final appearance of the embedded Tweet, be aware that the 
	 * appearance of the rendered Tweet may change over time to be consistent with Twitter's Display Requirements. Do not 
	 * rely on any class or id parameters to stay constant in the returned markup.
	 *
	 * @param int $id	The Tweet/status ID to return embed code for.
	 *
	 * @param int $maxwidth	The maximum width in pixels that the embed should be rendered at. This value is constrained 
	 *						to be between 250 and 550 pixels.
	 *
	 * @param boolean $hide_media	Specifies whether the embedded Tweet should automatically expand images which were 
	 *								uploaded via POST statuses/update_with_media. When set to either true, t or 1 images
	 *								will not be expanded. Defaults to false.
	 *
	 * @param boolean $hide_thread	Specifies whether the embedded Tweet should automatically show the original message 
	 *								in the case that the embedded Tweet is a reply. When set to either true, t or 1 the 
	 *								original Tweet will not be shown. Defaults to false.
	 *
	 * @param boolean $omit_script	Specifies whether the embedded Tweet HTML should include a <script> element pointing to
	 *								widgets.js. In cases where a page already includes widgets.js, setting this value to true 
	 *								will prevent a redundant script element from being included. When set to either true the
	 *								<script> element will not be included in the embed HTML, meaning that pages must include 
	 *								a reference to widgets.js manually. Defaults to false.
	 *
	 * @param string $align		Specifies whether the embedded Tweet should be left aligned, right aligned, or centered 
	 *							in the page. Valid values are left, right, center, and none. Defaults to none, meaning 
	 *							no alignment styles are specified for the Tweet.
	 *
	 * @param string $related	A value for the TWT related parameter, as described in Web Intents. This value will be 
	 *							forwarded to all Web Intents calls.
	 *							Example Values: twitterapi,twittermedia,twitter
	 *
	 * @param string $lang	Language code for the rendered embed. This will affect the text and localization of the 
	 *						rendered HTML.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getOEmbedById(
				$id		   ,
		   $maxwidth = NULL,
		 $hide_media = NULL,
		$hide_thread = NULL,
		$omit_script = NULL,
		      $align = NULL,
		    $related = NULL,
		       $lang = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['id'] = $id;
		$parameter_array['maxwidth'] = $maxwidth;
		$parameter_array['hide_media'] = $hide_media;
		$parameter_array['hide_thread'] = $hide_thread;
		$parameter_array['omit_script'] = $omit_script;
		$parameter_array['align'] = $align;
		$parameter_array['related'] = $related;
		$parameter_array['lang'] = $lang;
		
		$this->process_request('statuses/oembed', $parameter_array);
		
		return $this;
	}

	/**
	 * getOEmbedByURL:
	 *
	 * Returns information allowing the creation of an embedded representation of a Tweet on third party sites. See the oEmbed
	 * specification for information about the response format.
	 *
	 * While this endpoint allows a bit of customization for the final appearance of the embedded Tweet, be aware that the 
	 * appearance of the rendered Tweet may change over time to be consistent with Twitter's Display Requirements. Do not 
	 * rely on any class or id parameters to stay constant in the returned markup.
	 *
	 * @param int $url	The URL of the Tweet/status to be embedded.
	 *					Example Values:
	 *						To embed the Tweet at https://twitter.com/#!/twitter/status/99530515043983360, use:
	 *							https%3A%2F%2Ftwitter.com%2F%23!%2Ftwitter%2Fstatus%2F99530515043983360
	 *
	 *						To embed the Tweet at https://twitter.com/twitter/status/99530515043983360, use:
	 *							https%3A%2F%2Ftwitter.com%2Ftwitter%2Fstatus%2F99530515043983360
	 *
	 * @param int $maxwidth	The maximum width in pixels that the embed should be rendered at. This value is constrained 
	 *						to be between 250 and 550 pixels.
	 *
	 * @param boolean $hide_media	Specifies whether the embedded Tweet should automatically expand images which were 
	 *								uploaded via POST statuses/update_with_media. When set to either true, t or 1 images
	 *								will not be expanded. Defaults to false.
	 *
	 * @param boolean $hide_thread	Specifies whether the embedded Tweet should automatically show the original message 
	 *								in the case that the embedded Tweet is a reply. When set to either true, t or 1 the 
	 *								original Tweet will not be shown. Defaults to false.
	 *
	 * @param boolean $omit_script	Specifies whether the embedded Tweet HTML should include a <script> element pointing to
	 *								widgets.js. In cases where a page already includes widgets.js, setting this value to true 
	 *								will prevent a redundant script element from being included. When set to either true the
	 *								<script> element will not be included in the embed HTML, meaning that pages must include 
	 *								a reference to widgets.js manually. Defaults to false.
	 *
	 * @param string $align		Specifies whether the embedded Tweet should be left aligned, right aligned, or centered 
	 *							in the page. Valid values are left, right, center, and none. Defaults to none, meaning 
	 *							no alignment styles are specified for the Tweet.
	 *
	 * @param string $related	A value for the TWT related parameter, as described in Web Intents. This value will be 
	 *							forwarded to all Web Intents calls.
	 *							Example Values: twitterapi,twittermedia,twitter
	 *
	 * @param string $lang	Language code for the rendered embed. This will affect the text and localization of the 
	 *						rendered HTML.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getOEmbedByURL(
				$url	   ,
		   $maxwidth = NULL,
		 $hide_media = NULL,
		$hide_thread = NULL,
		$omit_script = NULL,
		      $align = NULL,
		    $related = NULL,
		       $lang = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['url'] = $url;
		$parameter_array['maxwidth'] = $maxwidth;
		$parameter_array['hide_media'] = $hide_media;
		$parameter_array['hide_thread'] = $hide_thread;
		$parameter_array['omit_script'] = $omit_script;
		$parameter_array['align'] = $align;
		$parameter_array['related'] = $related;
		$parameter_array['lang'] = $lang;
		
		$this->process_request('statuses/oembed', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getSearchTweets(
					   $q		,
				 $geocode = NULL,
					$lang = NULL,
				  $locale = NULL,
			 $result_type = NULL,
				   $count = NULL,
				   $until = NULL,
				$since_id = NULL,
				  $max_id = NULL,
		$include_entities = NULL,
				$callback = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['q'] = $q;
		$parameter_array['geocode'] = $geocode;
		$parameter_array['lang'] = $lang;
		$parameter_array['locale'] = $locale;
		$parameter_array['result_type'] = $result_type;
		$parameter_array['count'] = $count;
		$parameter_array['until'] = $until;
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['include_entities'] = $include_entities;
		$parameter_array['callback'] = $callback;
		
		$this->process_request('search/tweets', $parameter_array);
		
		return $this;
	}

	//-------------------- Skipping the streaming methods for now

	/**
	 */
	public function getDirectMessages(
				$since_id = NULL,
				  $max_id = NULL,
				   $count = NULL,
		$include_entities = NULL,
			 $skip_status = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['count'] = $count;
		$parameter_array['include_entities'] = $include_entities;
		$parameter_array['skip_status'] = $skip_status;
		
		$this->process_request('direct_messages', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getDirectMessagesSent(
				$since_id = NULL,
				  $max_id = NULL,
				   $count = NULL,
					$page = NULL,
		$include_entities = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['since_id'] = $since_id;
		$parameter_array['max_id'] = $max_id;
		$parameter_array['count'] = $count;
		$parameter_array['page'] = $page;
		$parameter_array['include_entities'] = $include_entities;
		
		$this->process_request('direct_messages/sent', $parameter_array);
		
		return $this;
	}

	/**
	 * getSingleDirectMessage:
	 *
	 * Returns a single direct message, specified by an id parameter. Like the /1.1/direct_messages.format request,
	 * this method will include the user objects of the sender and recipient.
	 *
	 * Important: This method requires an access token with RWD (read, write & direct message) permissions. Consult
	 * The Application Permission Model for more information.
	 *
	 * @param int $id	The ID of the direct message. Example Values: 587424932
	 *
	 * @return object $this object for further manipulations
	 */
	public function getSingleDirectMessage($id)
	{
		$parameter_array = array();
		$parameter_array['id'] = $id;
		
		$this->process_request('direct_messages/show', $parameter_array);
		
		return $this;
	}

	/**
	 *
	 *
	 * Returns a collection of user_ids that the currently authenticated user does not want to receive retweets from.
	 *
	 * @param boolean $stringify_ids	Many programming environments will not consume our ids due to their size. 
	 *									Provide this option to have ids returned as strings instead. Read more about 
	 *									Twitter IDs, JSON and Snowflake. This parameter is especially important to 
	 *									use in Javascript environments.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getBlockedRetweetIds($stringify_ids = true)
	{
		$parameter_array = array();
		$parameter_array['stringify_ids'] = $stringify_ids;
		
		$this->process_request('friendships/no_retweets/ids', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getFriendsIdsByUserId(
			  $user_id		 ,
			   $cursor = NULL,
		$stringify_ids = NULL,
				$count = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['user_id'] = $user_id;
		$parameter_array['cursor'] = $cursor;
		$parameter_array['stringify_ids'] = $stringify_ids;
		$parameter_array['count'] = $count;
		
		$this->process_request('friendshipts/friends/ids', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getFriendsIdsByScreenName(
		  $screen_name		 ,
			   $cursor = NULL,
		$stringify_ids = NULL,
				$count = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['user_id'] = $user_id;
		$parameter_array['cursor'] = $cursor;
		$parameter_array['stringify_ids'] = $stringify_ids;
		$parameter_array['count'] = $count;
		
		$this->process_request('followers/ids', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getFollowersIdsByUserId(
			  $user_id		 ,
			   $cursor = NULL,
		$stringify_ids = NULL,
				$count = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['user_id'] = $user_id;
		$parameter_array['cursor'] = $cursor;
		$parameter_array['stringify_ids'] = $stringify_ids;
		$parameter_array['count'] = $count;
		
		$this->process_request('followers/ids', $parameter_array);
		
		return $this;
	}

	/**
	 */
	public function getFollowersIdsByScreenName(
		  $screen_name		 ,
			   $cursor = NULL,
		$stringify_ids = NULL,
				$count = NULL
	)
	{
		$parameter_array = array();
		$parameter_array['screen_name'] = $screen_name;
		$parameter_array['cursor'] = $cursor;
		$parameter_array['stringify_ids'] = $stringify_ids;
		$parameter_array['count'] = $count;
		
		$this->process_request('friendships/friends/ids', $parameter_array);
		
		return $this;
	}

	/**
	 * getFriendshipsLookupByScreenName:
	 *
	 * Returns the relationships of the authenticating user to the comma-separated list of up to 100 screen_names or 
	 * user_ids provided. Values for connections can be: following, following_requested, followed_by, none.
	 *
	 * @param string screen_name	A comma separated list of screen names, up to 100 are allowed in a single request.
	 *
	 * @return object $this object for further manipulations
	 */
	// @todo screen separated? we may want to look into this
	public function getFriendshipsLookupByScreenName($screen_name)
	{
		$parameter_array = array();
		$parameter_array['screen_name'] = $screen_name;
		
		$this->process_request('friendships/lookup', $parameter_array);
		
		return $this;
	}

	/**
	 * getFriendshipsLookupByUserId:
	 *
	 * Returns the relationships of the authenticating user to the comma-separated list of up to 100 screen_names or 
	 * user_ids provided. Values for connections can be: following, following_requested, followed_by, none.
	 *
	 * @param string user_id	A comma separated list of user IDs, up to 100 are allowed in a single request.
	 *
	 * @return object $this object for further manipulations
	 */
	public function getFriendshipsLookupByUserId($user_id)
	{
		$parameter_array = array();
		$parameter_array['user_id'] = $user_id;
		
		$this->process_request('friendships/lookup', $parameter_array);
		
		return $this;
	}

	/**
	 * @return string JSON String of the data from Twitter
	 */
	public function fetchJSON()
	{
		return $this->result;
	}

	/**
	 * @return string JSON String of the data from Twitter
	 */
	public function __toString()
	{
		return $this->fetchJSON();
	}

	/**
	 * @return array array of the data from Twitter
	 */
	public function fetchArray()
	{
		return json_decode($this->result);
	}

	/**
	 * simple abstracted function to process the parameters and validate
	 *
	 * @param	string	$method				string path that defines the twitter method
	 * @param	array	$parameter_array	array of params to pass into twitter
	 * @return	boolean						boolean on whether or not a request was made
	 */
	private function process_request($method, $parameter_array)
	{
		$parameter_array = $this->clean_parameter_array($parameter_array);
		
		if($this->check_parameter_array($parameter_array) === false)
			return false;
		
		$this->result = $this->obcentoRequest->execute($method, $parameter_array);
		
		return true;
	}

	/**
	 * special case abstract function with the tweet id is part of the request endpoint
	 *
	 * @param	string	$method				string path that defines the main twitter method
	 * @param	int		$id					tweet id that needs to be validated
	 * @return	boolean						boolean on whether or not a request was made
	 */
	private function process_tweet_request($method, $id, $parameter_array)
	{
		// @todo add id validation
		
		return $this->process_request("{$method}/{$id}", $parameter_array);
	}

	/**
	 * goes through the params and removes null (default)
	 *
	 * @param	array	$array	array of params, with key => null that needs to be removed
	 * @return	array			clean array with no null values
	 */
	private function clean_parameter_array($array)
	{
		return array_filter($array, create_function('$value', 'return $value !== null;'));
	}

	/**
	 * goes through the params and validates
	 *
	 * @param	array	$array	array of clean params that need to be checked for validity
	 * @return	bool			simple true/false if the params are valid
	 */
	private function check_parameter_array($array)
	{
		foreach($array as $key => $value)
		{
			switch($key)
			{
				case 'contributor_details' :
					$is_valid = ObcentoTwitterValidateInput::check_contributor_details($value);
					break;
				case 'count' :
					$is_valid = ObcentoTwitterValidateInput::check_count($value);
					break;
				case 'exclude_replies' :
					$is_valid = ObcentoTwitterValidateInput::check_exclude_replies($value);
					break;
				case 'include_entities' :
					$is_valid = ObcentoTwitterValidateInput::check_include_entities($value);
					break;
				case 'include_rts' :
					$is_valid = ObcentoTwitterValidateInput::check_include_rts($value);
					break;
				case 'include_user_entities' :
					$is_valid = ObcentoTwitterValidateInput::check_include_user_entities($value);
					break;
				case 'max_id' :
					$is_valid = ObcentoTwitterValidateInput::check_max_id($value);
					break;
				case 'screen_name' :
					$is_valid = ObcentoTwitterValidateInput::check_screen_name($value);
					break;
				case 'since_id' :
					$is_valid = ObcentoTwitterValidateInput::check_since_id($value);
					break;
				case 'trim_user' :
					$is_valid = ObcentoTwitterValidateInput::check_trim_user($value);
					break;
				case 'user_id' :
					$is_valid = ObcentoTwitterValidateInput::check_user_id($value);
					break;
				default :
					$is_valid = false;
					break;
			}
			
			if($is_valid === false)
			{
				trigger_error("Parameter failed validation: {$key} -> {$value}. Request not attempted.");
				return false;
			}
		}
		return true;
	}

}