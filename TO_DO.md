# Document:

- Hooks
- Event Date block Slot/Fill usage
  - noting you have to use the provided meta/setMeta for the modal


> import { registerPlugin } from '@wordpress/plugins';
> import { AfterEventDetailsFill, AfterRecurringFill } from '../../../../../plugins/bluecadet-events/editor/components/_utils/slots.js';
> 
> registerPlugin( 'my-theme-event-dates', {
>     render                                            :  () => (
>         <>
>             <AfterEventDetailsFill>
>                 { ( { keys, postID, meta, setMeta } ) => (
>                     <div>
>                         Hook it
>                     </div>
>                 ) }
>             </AfterEventDetailsFill>
> 
>             <AfterRecurringFill>
>                 <div>Extra recurring content</div>
>             </AfterRecurringFill>
>         </>
>     ),


# Tests:

- Recurring date


