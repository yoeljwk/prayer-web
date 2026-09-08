import { ref, watch, type Ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

/**
 * Syncs a reactive ref with a single URL query parameter.
 *
 * Behavior:
 * - Initializes from the current URL query on setup.
 * - When state changes (user clicks UI), updates the URL via router.replace
 *   (no new history entry, preserves other query params).
 * - When URL changes (Back/Forward navigation), updates state accordingly.
 * - Removes the key from the URL when value equals defaultValue (keeps URLs clean).
 *
 * Circular-update is prevented by the guard `parsed !== state.value` in the
 * URL→State watcher, so the two watchers don't loop each other.
 */
export function useQueryState<T extends string>(key: string, defaultValue: T): Ref<T> {
  const route = useRoute()
  const router = useRouter()

  // Initialize from URL; fall back to defaultValue if not present
  const state = ref<T>((route.query[key] as T) || defaultValue) as Ref<T>

  // State → URL (triggered by user UI interaction)
  watch(state, (val) => {
    const query = { ...route.query }
    if (val === defaultValue) {
      // Keep URLs clean: remove the param when it matches the default
      delete query[key]
    } else {
      query[key] = val
    }
    router.replace({ query })
  })

  // URL → State (triggered by Back/Forward navigation)
  watch(
    () => route.query[key],
    (val) => {
      const parsed = (val as T) || defaultValue
      // Guard prevents circular update: if state is already correct, skip
      if (parsed !== state.value) {
        state.value = parsed
      }
    },
  )

  return state
}
